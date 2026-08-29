<?php
/**
 * Corrigé 9.D — Le manager générique
 * Factorisation par classe abstraite + instanciation dynamique.
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

// ═══════════════════════════ 1. Le manager abstrait ═══════════════════════════

abstract class AbstractManager
{
    public function __construct(protected PDO $pdo) {}

    /**
     * Les deux seules choses que chaque manager concret doit fournir.
     * Le reste est écrit UNE FOIS ici.
     */
    abstract protected function getTable(): string;

    /** @return class-string<AbstractModel> */
    abstract protected function getModelClass(): string;

    /** @return AbstractModel[] */
    public function findAll(): array
    {
        // ⚠️ getTable() vient du CODE, jamais d'une saisie : voir la
        // question de réflexion en bas de fichier.
        $stmt = $this->pdo->query('SELECT * FROM ' . $this->getTable());

        $classe = $this->getModelClass();

        // Instanciation DYNAMIQUE : new $classe($row) crée le bon modèle
        // sans que AbstractManager ne connaisse Score, Creature ou autre.
        return array_map(
            fn(array $row) => new $classe($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findById(int $id): ?AbstractModel
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ' . $this->getTable() . ' WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $classe = $this->getModelClass();

        return new $classe($row);
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM ' . $this->getTable() . ' WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->pdo
            ->query('SELECT COUNT(*) FROM ' . $this->getTable())
            ->fetchColumn();
    }
}

// ═══════════════════════════ Deux modèles ═══════════════════════════

class Score extends AbstractModel
{
    private ?int $id = null;
    private string $pseudo = '';
    private int $points = 0;
    private string $jeu = '';

    public function getId(): ?int { return $this->id; }
    public function getPseudo(): string { return $this->pseudo; }
    public function getPoints(): int { return $this->points; }
    public function getJeu(): string { return $this->jeu; }

    public function setId(int|string|null $v): void { $this->id = $v === null ? null : (int) $v; }
    public function setPseudo(string $v): void { $this->pseudo = substr(strtoupper(trim($v)), 0, 3); }
    public function setPoints(int|string $v): void { $this->points = max(0, (int) $v); }
    public function setJeu(string $v): void { $this->jeu = trim($v); }

    public function __toString(): string
    {
        return sprintf('%s — %s pts (%s)', $this->pseudo,
            number_format($this->points, 0, ',', ' '), $this->jeu);
    }
}

class Creature extends AbstractModel
{
    private ?int $id = null;
    private string $name = '';
    private string $espece = '';
    private int $niveauDanger = 1;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEspece(): string { return $this->espece; }
    public function getNiveauDanger(): int { return $this->niveauDanger; }

    public function setId(int|string|null $v): void { $this->id = $v === null ? null : (int) $v; }
    public function setName(string $v): void { $this->name = trim($v); }
    public function setEspece(string $v): void { $this->espece = strtolower(trim($v)); }
    public function setNiveauDanger(int|string $v): void { $this->niveauDanger = max(1, min(5, (int) $v)); }

    public function __toString(): string
    {
        return sprintf('%s (%s) — danger %d/5', $this->name, $this->espece, $this->niveauDanger);
    }
}

// ═════════════════════ 3 & 4. Deux managers ultra-légers ═════════════════════

class ScoreManager extends AbstractManager
{
    #[\Override]
    protected function getTable(): string
    {
        return 'score';
    }

    #[\Override]
    protected function getModelClass(): string
    {
        return Score::class;
    }

    // ── Seules les méthodes SPÉCIFIQUES restent à écrire :

    /** @return Score[] */
    public function getTop(int $limite = 3): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM score ORDER BY points DESC LIMIT :limite');
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn(array $r) => new Score($r), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

class CreatureManager extends AbstractManager
{
    #[\Override]
    protected function getTable(): string
    {
        return 'creature';
    }

    #[\Override]
    protected function getModelClass(): string
    {
        return Creature::class;
    }

    /** @return Creature[] */
    public function getDangereuses(int $seuil = 4): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM creature WHERE niveau_danger >= :s');
        $stmt->execute(['s' => $seuil]);

        return array_map(fn(array $r) => new Creature($r), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

// ═══════════════════════════════ Démonstration ═══════════════════════════════

$pdo = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$pdo->exec('CREATE TABLE score (id INTEGER PRIMARY KEY AUTOINCREMENT,
            pseudo VARCHAR(3), points INT, jeu VARCHAR(50))');
$pdo->exec('CREATE TABLE creature (id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100), espece VARCHAR(50), niveau_danger INT)');

$pdo->exec("INSERT INTO score (pseudo, points, jeu) VALUES
            ('AAA', 999999, 'PAC-POO'), ('BOB', 512340, 'PAC-POO'),
            ('XYZ', 128000, 'TETRIS'),  ('PHP', 42000, 'TETRIS')");
$pdo->exec("INSERT INTO creature (name, espece, niveau_danger) VALUES
            ('Fumerolle', 'dragon', 5), ('Éclat', 'licorne', 1),
            ('Braise', 'phenix', 4)");

$scores = new ScoreManager($pdo);
$creatures = new CreatureManager($pdo);

echo '🏗️  LE MANAGER GÉNÉRIQUE' . PHP_EOL . PHP_EOL;

echo '── findAll() hérité, sur DEUX tables différentes ──' . PHP_EOL;

echo '   Scores :' . PHP_EOL;
foreach ($scores->findAll() as $score) {
    echo '     • ' . $score . PHP_EOL;
}

echo '   Créatures :' . PHP_EOL;
foreach ($creatures->findAll() as $creature) {
    echo '     • ' . $creature . PHP_EOL;
}

echo PHP_EOL . '── count() et findById() hérités ──' . PHP_EOL;
printf("   %d scores, %d créatures%s", $scores->count(), $creatures->count(), PHP_EOL);
printf("   findById(2) sur score    : %s%s", $scores->findById(2), PHP_EOL);
printf("   findById(1) sur creature : %s%s", $creatures->findById(1), PHP_EOL);
printf("   findById(999)            : %s%s",
    var_export($scores->findById(999), true), PHP_EOL);

echo PHP_EOL . '── Les méthodes spécifiques ──' . PHP_EOL;
echo '   Top 2 des scores :' . PHP_EOL;
foreach ($scores->getTop(2) as $s) {
    echo '     • ' . $s . PHP_EOL;
}
echo '   Créatures dangereuses :' . PHP_EOL;
foreach ($creatures->getDangereuses() as $c) {
    echo '     • ' . $c . PHP_EOL;
}

echo PHP_EOL . '── deleteById() hérité ──' . PHP_EOL;
$creatures->deleteById(2);
printf("   Après suppression : %d créatures%s", $creatures->count(), PHP_EOL);

// ═══════════════════════ 5. La question de réflexion ═══════════════════════

echo PHP_EOL . <<<'TXT'
    📉 COMBIEN DE CODE ÉCONOMISÉ ?

       Sans AbstractManager : 4 méthodes CRUD × N managers.
       Avec : 2 méthodes triviales (getTable, getModelClass) par manager.
       À 5 tables, l'économie dépasse les 150 lignes — et surtout, une
       correction de bug dans findAll() profite instantanément à tous.

    ⚠️  5. LA LIMITE : LE NOM DE TABLE N'EST PAS UN PARAMÈTRE

       Regardez findAll() :

           $this->pdo->query('SELECT * FROM ' . $this->getTable());

       C'est une CONCATÉNATION — exactement ce qu'on s'interdit ailleurs !
       Ici c'est acceptable, et seulement parce que getTable() renvoie une
       chaîne littérale écrite par le développeur, jamais une donnée
       extérieure.

       POURQUOI NE JAMAIS ACCEPTER UN NOM DE TABLE VENU DE L'UTILISATEUR ?
       Parce que SQL n'autorise PAS de placeholder pour un identifiant :
       `SELECT * FROM :table` est une erreur de syntaxe. Impossible donc de
       s'en remettre à la requête préparée. Un ?table=score;DROP+TABLE+users
       serait catastrophique.

       Si vous devez vraiment choisir une table dynamiquement, la seule
       méthode sûre est la LISTE BLANCHE :

           $tables = ['score' => 'score', 'creature' => 'creature'];
           $table = $tables[$saisie] ?? throw new InvalidArgumentException();

       La saisie ne sert alors qu'à CHOISIR parmi des valeurs que vous avez
       écrites vous-même. La même règle vaut pour ORDER BY et les noms de
       colonnes. 🔒

    🎯 AUTRES LIMITES DE CETTE APPROCHE
       - findAll() sur une grosse table charge TOUT en mémoire ;
       - aucune gestion des jointures ni du chargement paresseux ;
       - la clé primaire est supposée s'appeler « id ».
       C'est précisément là que commencent les vrais ORM (Doctrine…) —
       mais vous savez désormais ce qu'ils font sous le capot. 🔧
    TXT . PHP_EOL;
