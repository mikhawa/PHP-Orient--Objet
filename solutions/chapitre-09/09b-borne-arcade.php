<?php
/**
 * Corrigé 9.B — Les records de la borne d'arcade
 * LIMIT paramétré, LIKE sécurisé, GROUP BY, et démonstration d'injection SQL.
 */

declare(strict_types=1);

abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        foreach ($tab as $clef => $valeur) {
            $methode = 'set' . str_replace('_', '', ucwords((string) $clef, '_'));
            if (method_exists($this, $methode)) {
                $this->$methode($valeur);
            }
        }
    }
}

class Score extends AbstractModel
{
    private ?int $id = null;
    private string $pseudo = 'AAA';
    private int $points = 0;
    private string $jeu = '';
    private string $joueLe = '';

    public function getId(): ?int { return $this->id; }
    public function getPseudo(): string { return $this->pseudo; }
    public function getPoints(): int { return $this->points; }
    public function getJeu(): string { return $this->jeu; }
    public function getJoueLe(): string { return $this->joueLe; }

    public function setId(int|string|null $id): void
    {
        $this->id = $id === null ? null : (int) $id;
    }

    /** Les bornes d'arcade n'acceptent que 3 lettres, en majuscules. */
    public function setPseudo(string $pseudo): void
    {
        $propre = strtoupper(trim($pseudo));
        $propre = preg_replace('/[^A-Z0-9]/', '', $propre) ?? '';

        if ($propre === '') {
            throw new InvalidArgumentException('Pseudo vide après nettoyage.');
        }

        $this->pseudo = substr($propre, 0, 3);
    }

    public function setPoints(int|string $points): void
    {
        $valeur = (int) $points;

        if ($valeur < 0) {
            throw new InvalidArgumentException("Un score négatif ? Impossible : $valeur");
        }

        $this->points = $valeur;
    }

    public function setJeu(string $jeu): void
    {
        $this->jeu = trim(strip_tags($jeu));
    }

    public function setJoueLe(string $date): void
    {
        $this->joueLe = $date;
    }
}

class ScoreManager
{
    public function __construct(private PDO $pdo) {}

    public function add(Score $score): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO score (pseudo, points, jeu, joue_le)
             VALUES (:pseudo, :points, :jeu, :joue_le)'
        );

        $stmt->execute([
            'pseudo'  => $score->getPseudo(),
            'points'  => $score->getPoints(),
            'jeu'     => $score->getJeu(),
            'joue_le' => $score->getJoueLe() ?: date('Y-m-d H:i:s'),
        ]);

        $score->setId((int) $this->pdo->lastInsertId());
    }

    public function getById(int $id): ?Score
    {
        $stmt = $this->pdo->prepare('SELECT * FROM score WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : new Score($row);
    }

    /**
     * 🚨 LE PIÈGE DU LIMIT
     *
     * On ne met JAMAIS une variable directement dans la requête, même un
     * entier « inoffensif » comme une limite.
     *
     * MAIS : avec execute(['limite' => $n]), PDO passe la valeur comme une
     * CHAÎNE. MySQL reçoit alors `LIMIT '10'` et refuse (erreur de syntaxe).
     * Il faut donc bindValue() avec PDO::PARAM_INT pour forcer le type.
     *
     * (SQLite est plus tolérant, mais on écrit le code correct pour MySQL :
     *  c'est là que vos stagiaires déploieront.)
     */
    public function getTop(int $limite = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM score ORDER BY points DESC, joue_le ASC LIMIT :limite'
        );

        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            fn(array $row) => new Score($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function getByJeu(string $jeu): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM score WHERE jeu = :jeu ORDER BY points DESC'
        );
        $stmt->execute(['jeu' => $jeu]);

        return array_map(
            fn(array $row) => new Score($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM score WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * 4. LIKE sécurisé : les % font partie de la VALEUR, pas de la requête.
     */
    public function chercherParPseudo(string $pseudo): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM score WHERE pseudo LIKE :motif ORDER BY points DESC'
        );

        // Le motif est un paramètre : même ' OR 1=1 -- ne sera qu'un texte.
        $stmt->execute(['motif' => '%' . $pseudo . '%']);

        return array_map(
            fn(array $row) => new Score($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /** Bonus : statistiques par jeu. */
    public function statistiquesParJeu(): array
    {
        return $this->pdo->query(
            'SELECT jeu,
                    COUNT(*)     AS parties,
                    MAX(points)  AS meilleur,
                    AVG(points)  AS moyenne
               FROM score
              GROUP BY jeu
              ORDER BY meilleur DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ═════════════════════════════ Le scénario ═════════════════════════════

$pdo = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$pdo->exec('
    CREATE TABLE score (
        id      INTEGER PRIMARY KEY AUTOINCREMENT,
        pseudo  VARCHAR(3)  NOT NULL,
        points  INT         NOT NULL DEFAULT 0,
        jeu     VARCHAR(50) NOT NULL,
        joue_le DATETIME    NOT NULL
    )
');

$manager = new ScoreManager($pdo);

$parties = [
    ['pseudo' => 'aaa',        'points' => 999999, 'jeu' => 'PAC-POO'],
    ['pseudo' => 'Bob',        'points' => 512340, 'jeu' => 'PAC-POO'],
    ['pseudo' => 'xyz!!',      'points' => 128000, 'jeu' => 'PAC-POO'],
    ['pseudo' => 'php',        'points' =>  42000, 'jeu' => 'PAC-POO'],
    ['pseudo' => 'zoé',        'points' =>   1337, 'jeu' => 'PAC-POO'],
    ['pseudo' => 'sql',        'points' => 777777, 'jeu' => 'SPACE INVADERS'],
    ['pseudo' => 'ada',        'points' => 654321, 'jeu' => 'SPACE INVADERS'],
    ['pseudo' => 'ARB',        'points' =>  99999, 'jeu' => 'SPACE INVADERS'],
    ['pseudo' => 'moi',        'points' =>     10, 'jeu' => 'TETRIS'],
    ['pseudo' => 'toi',        'points' =>  50000, 'jeu' => 'TETRIS'],
];

foreach ($parties as $i => $donnees) {
    $donnees['joue_le'] = date('Y-m-d H:i:s', time() - (10 - $i) * 3600);
    $manager->add(new Score($donnees));
}

// ── Le hall of fame
echo '🕹️  HALL OF FAME — PAC-POO  🕹️' . PHP_EOL . PHP_EOL;

$medailles = ['🥇', '🥈', '🥉'];

foreach ($manager->getByJeu('PAC-POO') as $rang => $score) {
    printf(
        "%s %s %s %s pts%s",
        $medailles[$rang] ?? sprintf('%2d.', $rang + 1),
        $score->getPseudo(),
        str_repeat('.', 25 - strlen(number_format($score->getPoints(), 0, ',', ' '))),
        number_format($score->getPoints(), 0, ',', ' '),
        PHP_EOL
    );
}

echo PHP_EOL . '   💡 Les pseudos ont été normalisés par le setter :' . PHP_EOL;
echo '      "aaa" → AAA · "xyz!!" → XYZ · "zoé" → ZO (é retiré) · "Bob" → BOB' . PHP_EOL;

// ── Le top toutes catégories
echo PHP_EOL . '🏆 TOP 5 TOUTES CATÉGORIES (LIMIT paramétré)' . PHP_EOL;
foreach ($manager->getTop(5) as $rang => $score) {
    printf("   %d. %s — %s pts (%s)%s",
        $rang + 1, $score->getPseudo(),
        number_format($score->getPoints(), 0, ',', ' '),
        $score->getJeu(), PHP_EOL);
}

// ── 4. La tentative d'injection SQL
echo PHP_EOL . '💣 4. TENTATIVE D\'INJECTION SQL' . PHP_EOL . PHP_EOL;

$malveillant = "' OR 1=1 --";

echo "   Recherche du pseudo : \"$malveillant\"" . PHP_EOL;
$resultats = $manager->chercherParPseudo($malveillant);
printf("   → %d résultat(s). La requête préparée a traité l'attaque comme%s",
    count($resultats), PHP_EOL);
echo '     un simple texte à chercher. Aucune ligne ne correspond. ✅' . PHP_EOL;

echo PHP_EOL . '   Et si on avait CONCATÉNÉ (ne faites jamais ça) :' . PHP_EOL;

$sqlDangereux = "SELECT * FROM score WHERE pseudo LIKE '%$malveillant%'";
echo "   SQL généré : $sqlDangereux" . PHP_EOL;

$lignes = $pdo->query($sqlDangereux)->fetchAll(PDO::FETCH_ASSOC);
printf("   → %d résultat(s) : TOUTE LA TABLE est exposée. 💥%s", count($lignes), PHP_EOL);

echo PHP_EOL . '   Décomposons l\'attaque :' . PHP_EOL;
echo <<<'TXT'

       WHERE pseudo LIKE '%' OR 1=1 --%'
                          │   │      │
                          │   │      └─ « -- » commente la fin : le reste
                          │   │         de la requête est ignoré
                          │   └──────── condition TOUJOURS vraie,
                          │             donc toutes les lignes sortent
                          └──────────── la quote ferme prématurément
                                        la chaîne prévue par le développeur

TXT;

echo PHP_EOL . '   Avec un DROP TABLE ou un UPDATE, c\'est la fin de la base.' . PHP_EOL;
echo '   👉 Requête préparée. Toujours. Sans exception. 🔒' . PHP_EOL;

// ── Bonus : statistiques
echo PHP_EOL . '📊 BONUS : STATISTIQUES PAR JEU' . PHP_EOL . PHP_EOL;
printf("   %-16s %-9s %-12s %s%s", 'JEU', 'PARTIES', 'MEILLEUR', 'MOYENNE', PHP_EOL);
printf("   %s%s", str_repeat('─', 50), PHP_EOL);

foreach ($manager->statistiquesParJeu() as $ligne) {
    printf(
        "   %-16s %-9d %-12s %s%s",
        $ligne['jeu'],
        $ligne['parties'],
        number_format((int) $ligne['meilleur'], 0, ',', ' '),
        number_format((float) $ligne['moyenne'], 0, ',', ' '),
        PHP_EOL
    );
}
