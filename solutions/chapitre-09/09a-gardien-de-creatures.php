<?php
/**
 * Corrigé 9.A — Le Gardien de Créatures
 * Modèle + Manager PDO complet, sur SQLite (aucune installation requise).
 *
 * Pour passer à MySQL, seule la ligne du DSN change (voir tout en bas).
 */

declare(strict_types=1);

// ═══════════════════════════════ 2. L'enum ═══════════════════════════════

enum Espece: string
{
    case Dragon = 'dragon';
    case Licorne = 'licorne';
    case Gobelin = 'gobelin';
    case Phenix = 'phenix';

    public function emoji(): string
    {
        return match ($this) {
            Espece::Dragon  => '🐉',
            Espece::Licorne => '🦄',
            Espece::Gobelin => '👺',
            Espece::Phenix  => '🔥',
        };
    }

    public function cri(): string
    {
        return match ($this) {
            Espece::Dragon  => 'GROOAAAR !',
            Espece::Licorne => '*hennissement cristallin*',
            Espece::Gobelin => 'Kihihi ! Mes sous !',
            Espece::Phenix  => '*chant qui donne la chair de poule*',
        };
    }
}

// ═══════════════════════════ Le socle d'hydratation ═══════════════════════════

abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        $this->hydrate($tab);
    }

    protected function hydrate(array $assoc): void
    {
        foreach ($assoc as $clef => $valeur) {
            $methode = 'set' . str_replace('_', '', ucwords((string) $clef, '_'));
            if (method_exists($this, $methode)) {
                $this->$methode($valeur);
            }
        }
    }
}

// ═══════════════════════════════ 3. Le modèle ═══════════════════════════════

class Creature extends AbstractModel
{
    private ?int $id = null;
    private string $name = '';
    private Espece $espece = Espece::Gobelin;
    private int $niveauDanger = 1;
    private bool $estEndormie = false;

    // ── Getters
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEspece(): Espece { return $this->espece; }
    public function getNiveauDanger(): int { return $this->niveauDanger; }
    public function isEstEndormie(): bool { return $this->estEndormie; }

    // ── Setters
    public function setId(int|string|null $id): void
    {
        $this->id = $id === null ? null : (int) $id;
    }

    public function setName(string $name): void
    {
        $propre = trim(strip_tags($name));
        if ($propre === '') {
            throw new InvalidArgumentException('Une créature doit avoir un nom.');
        }
        $this->name = $propre;
    }

    /** La frontière SQL → objet : la chaîne devient un enum. */
    public function setEspece(Espece|string $espece): void
    {
        $this->espece = $espece instanceof Espece
            ? $espece
            : (Espece::tryFrom(strtolower(trim($espece)))
                ?? throw new InvalidArgumentException("Espèce inconnue : '$espece'"));
    }

    /** Le niveau de danger est BORNÉ entre 1 et 5, quoi qu'il arrive. */
    public function setNiveauDanger(int|string $niveau): void
    {
        $this->niveauDanger = max(1, min(5, (int) $niveau));
    }

    public function setEstEndormie(bool|int|string $v): void
    {
        $this->estEndormie = (bool) (int) $v;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s (%s) — danger %d/5 — %s',
            $this->espece->emoji(),
            $this->name,
            $this->espece->value,
            $this->niveauDanger,
            $this->estEndormie ? '😴 endormie' : '👁️  éveillée'
        );
    }
}

// ═══════════════════════════════ 4. Le manager ═══════════════════════════════

class CreatureManager
{
    // Injection de dépendance : le manager REÇOIT sa connexion.
    // Il ne la crée pas → on peut le tester avec une base en mémoire.
    public function __construct(private PDO $pdo) {}

    public function getCreature(int $id): ?Creature
    {
        $stmt = $this->pdo->prepare('SELECT * FROM creature WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : new Creature($row);
    }

    /** @return Creature[] */
    public function getCreatures(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM creature ORDER BY niveau_danger DESC, name');

        $creatures = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $creatures[] = new Creature($row);
        }

        return $creatures;
    }

    public function addCreature(Creature $creature): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO creature (name, espece, niveau_danger, est_endormie)
             VALUES (:name, :espece, :niveau_danger, :est_endormie)'
        );

        $stmt->execute([
            'name'          => $creature->getName(),
            'espece'        => $creature->getEspece()->value,  // enum → string
            'niveau_danger' => $creature->getNiveauDanger(),
            'est_endormie'  => (int) $creature->isEstEndormie(), // bool → 0/1
        ]);

        // On complète l'objet avec l'id généré par la base.
        $creature->setId((int) $this->pdo->lastInsertId());
    }

    public function updateCreature(Creature $creature): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE creature
                SET name = :name, espece = :espece,
                    niveau_danger = :niveau_danger, est_endormie = :est_endormie
              WHERE id = :id'
        );

        $stmt->execute([
            'id'            => $creature->getId(),
            'name'          => $creature->getName(),
            'espece'        => $creature->getEspece()->value,
            'niveau_danger' => $creature->getNiveauDanger(),
            'est_endormie'  => (int) $creature->isEstEndormie(),
        ]);
    }

    public function deleteCreature(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM creature WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    // ── Les deux méthodes bonus

    /** @return Creature[] */
    public function getCreaturesDangereuses(int $seuil = 4): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM creature WHERE niveau_danger >= :seuil ORDER BY niveau_danger DESC'
        );
        $stmt->execute(['seuil' => $seuil]);

        return array_map(
            fn(array $row) => new Creature($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /** UPDATE ciblé : on ne recharge pas l'objet entier pour un seul champ. */
    public function berceuse(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE creature SET est_endormie = 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** Bonus : le rapport du ministère (GROUP BY). */
    public function getStatistiques(): array
    {
        $stmt = $this->pdo->query(
            'SELECT espece,
                    COUNT(*)            AS nombre,
                    AVG(niveau_danger)  AS danger_moyen,
                    MAX(niveau_danger)  AS danger_max
               FROM creature
              GROUP BY espece
              ORDER BY nombre DESC, espece'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ═══════════════════════════ 5. Le scénario du refuge ═══════════════════════════

// SQLite en mémoire : la base disparaît à la fin du script.
// Remplacez par 'sqlite:' . __DIR__ . '/refuge.sqlite' pour un fichier persistant.
$pdo = new PDO('sqlite::memory:', options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('
    CREATE TABLE creature (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        name          VARCHAR(100) NOT NULL,
        espece        VARCHAR(50)  NOT NULL,
        niveau_danger INT          NOT NULL DEFAULT 1,
        est_endormie  TINYINT(1)   NOT NULL DEFAULT 0
    )
');

$manager = new CreatureManager($pdo);

// ── Peupler le refuge
echo '🏰 LE REFUGE POUR CRÉATURES FANTASTIQUES' . PHP_EOL . PHP_EOL;
echo '── Arrivée des pensionnaires ──' . PHP_EOL;

$pensionnaires = [
    ['name' => 'Fumerolle',  'espece' => 'dragon',  'niveau_danger' => 5],
    ['name' => 'Éclat',      'espece' => 'licorne', 'niveau_danger' => 1],
    ['name' => 'Groin',      'espece' => 'gobelin', 'niveau_danger' => 2],
    ['name' => 'Braise',     'espece' => 'phenix',  'niveau_danger' => 4],
    ['name' => 'Croquemitou','espece' => 'dragon',  'niveau_danger' => 99], // sera borné à 5
];

foreach ($pensionnaires as $donnees) {
    $creature = new Creature($donnees);
    $manager->addCreature($creature);
    printf("   ✅ #%d %s accueilli·e%s", $creature->getId(), $creature->getName(), PHP_EOL);
}

// ── Le registre
echo PHP_EOL . '📖 REGISTRE DU REFUGE' . PHP_EOL;
foreach ($manager->getCreatures() as $creature) {
    echo '   ' . $creature . PHP_EOL;
}

echo PHP_EOL . '   💡 Croquemitou était annoncé au niveau 99 : le setter l\'a borné à 5.' . PHP_EOL;

// ── Endormir les dangereuses
echo PHP_EOL . '🎵 BERCEUSE POUR LES CRÉATURES DANGEREUSES (niveau ≥ 4)' . PHP_EOL;

foreach ($manager->getCreaturesDangereuses() as $creature) {
    $manager->berceuse($creature->getId());
    printf("   😴 %s %s endormi·e%s", $creature->getEspece()->emoji(), $creature->getName(), PHP_EOL);
}

echo PHP_EOL . '📖 REGISTRE APRÈS LA BERCEUSE' . PHP_EOL;
foreach ($manager->getCreatures() as $creature) {
    echo '   ' . $creature . PHP_EOL;
}

// ── L'évasion
echo PHP_EOL . '🚨 ALERTE : UN GOBELIN S\'EST ÉCHAPPÉ !' . PHP_EOL . PHP_EOL;

$gobelin = null;
foreach ($manager->getCreatures() as $creature) {
    if ($creature->getEspece() === Espece::Gobelin) {
        $gobelin = $creature;
        break;
    }
}

$manager->deleteCreature($gobelin->getId());

echo <<<TXT
   ╔══════════════════════════════════════════════╗
   ║           📜 AVIS DE RECHERCHE 📜            ║
   ╠══════════════════════════════════════════════╣
   ║  {$gobelin->getEspece()->emoji()}  {$gobelin->getName()}, gobelin de son état             ║
   ║  Signalement : « {$gobelin->getEspece()->cri()} »       ║
   ║  Récompense : 3 pièces d'or (ou une gaufre)  ║
   ╚══════════════════════════════════════════════╝

TXT;

printf("   Il reste %d créatures au refuge.%s", count($manager->getCreatures()), PHP_EOL);

// ── Le rapport du ministère
echo PHP_EOL . '📊 RAPPORT DU MINISTÈRE DES CRÉATURES MAGIQUES' . PHP_EOL . PHP_EOL;
printf("   %-10s %-8s %-14s %s%s", 'ESPÈCE', 'NOMBRE', 'DANGER MOYEN', 'DANGER MAX', PHP_EOL);
printf("   %s%s", str_repeat('─', 46), PHP_EOL);

foreach ($manager->getStatistiques() as $ligne) {
    $espece = Espece::from($ligne['espece']);
    printf(
        "   %s %-8s %-8d %-14.1f %d%s",
        $espece->emoji(),
        $espece->value,
        $ligne['nombre'],
        (float) $ligne['danger_moyen'],
        $ligne['danger_max'],
        PHP_EOL
    );
}

echo PHP_EOL . <<<'TXT'
    💡 POUR PASSER À MySQL, UNE SEULE LIGNE CHANGE :

       $pdo = new PDO(
           'mysql:host=localhost;dbname=refuge;charset=utf8mb4',
           'root',
           '',
           [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
       );

       …et dans le CREATE TABLE, INTEGER PRIMARY KEY AUTOINCREMENT redevient
       INT AUTO_INCREMENT PRIMARY KEY. TOUT le reste — modèle, manager,
       requêtes préparées — est identique. C'est exactement ce que PDO
       promet : le même code objet pour n'importe quel SGBD. 🔌
    TXT . PHP_EOL;
