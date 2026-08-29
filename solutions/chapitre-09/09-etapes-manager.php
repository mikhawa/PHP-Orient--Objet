<?php
/**
 * Corrigé 9.1 à 9.6 — Le manager, étape par étape
 * Tourne sur SQLite en mémoire : aucun serveur à installer.
 */

declare(strict_types=1);

// ═══════════════════════ Le modèle (chapitre 8) ═══════════════════════

class User
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = trim($name); }
}

// ═══════════════════════ Le manager ═══════════════════════

class UserManager
{
    // Le manager REÇOIT la connexion (on parle d'injection de dépendance).
    // Avantage : on peut lui donner une base de test sans rien changer.
    public function __construct(private PDO $pdo) {}

    // ── 9.2 : lire tout
    /** @return User[] */
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM user');
        $users = [];

        while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($ligne['id'], $ligne['name'], $ligne['email']);
        }

        return $users;
    }

    // ── 9.3 : lire un seul (ou rien)
    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);

        // fetch() renvoie false quand il n'y a aucune ligne
        if ($ligne === false) {
            return null;
        }

        return new User($ligne['id'], $ligne['name'], $ligne['email']);
    }

    // ── 9.4 : créer
    public function add(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user (name, email) VALUES (:name, :email)'
        );

        $stmt->execute([
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
        ]);

        // On complète l'objet avec l'id que la base vient de générer
        $user->setId((int) $this->pdo->lastInsertId());
    }

    // ── 9.5 : modifier
    public function update(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE user SET name = :name, email = :email WHERE id = :id'
        );

        $stmt->execute([
            'id'    => $user->getId(),
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }

    // ── 9.5 : supprimer
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    // ── 9.6 : la bonne façon de chercher
    /** @return User[] */
    public function chercherParNom(string $nom): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE name = :nom');
        $stmt->execute(['nom' => $nom]);

        return array_map(
            fn(array $l) => new User($l['id'], $l['name'], $l['email']),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}

// ═══════════════════════ 9.1 : la base ═══════════════════════

$pdo = new PDO('sqlite::memory:', options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('
    CREATE TABLE user (
        id    INTEGER PRIMARY KEY AUTOINCREMENT,
        name  VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL
    )
');

$pdo->exec("INSERT INTO user (name, email) VALUES
    ('Aline', 'aline@example.com'),
    ('Bob', 'bob@example.com')");

$manager = new UserManager($pdo);

// ═══════════════════════ 9.2 ═══════════════════════

echo '── 9.2 : getAll() ──' . PHP_EOL;

foreach ($manager->getAll() as $user) {
    echo '  - ' . $user->getName() . PHP_EOL;
}

// ═══════════════════════ 9.3 ═══════════════════════

echo PHP_EOL . '── 9.3 : getById() ──' . PHP_EOL;

echo '  id 1   : ' . ($manager->getById(1)?->getName() ?? 'introuvable') . PHP_EOL;
echo '  id 999 : ' . ($manager->getById(999)?->getName() ?? 'introuvable') . PHP_EOL;

// ═══════════════════════ 9.4 ═══════════════════════

echo PHP_EOL . '── 9.4 : add() ──' . PHP_EOL;

$carla = new User(0, 'Carla', 'carla@example.com');
echo '  Avant l\'insert, son id est : ' . $carla->getId() . PHP_EOL;

$manager->add($carla);
echo '  Après l\'insert (lastInsertId) : ' . $carla->getId() . PHP_EOL;

foreach ($manager->getAll() as $user) {
    echo '  - ' . $user->getName() . PHP_EOL;
}

// ═══════════════════════ 9.5 ═══════════════════════

echo PHP_EOL . '── 9.5 : update() et delete() ──' . PHP_EOL;

$aline = $manager->getById(1);
$aline->setName('Aline la Grande');
$manager->update($aline);

echo '  Après update : ' . $manager->getById(1)->getName() . PHP_EOL;

$manager->delete(2);
echo '  Après delete : ' . count($manager->getAll()) . ' utilisateurs restants' . PHP_EOL;

// ═══════════════════════ 9.6 : la sécurité ═══════════════════════

echo PHP_EOL . '── 9.6 : requête préparée contre concaténation ──' . PHP_EOL;

$attaque = "' OR 1=1 --";

// ── La bonne façon : requête préparée
$resultats = $manager->chercherParNom($attaque);
echo '  Requête PRÉPARÉE  : ' . count($resultats) . ' résultat(s) ✅' . PHP_EOL;
echo '    (aucun utilisateur ne porte ce nom — normal)' . PHP_EOL;

// ── La mauvaise façon, UNIQUEMENT pour la démonstration
$sqlDangereux = "SELECT * FROM user WHERE name = '$attaque'";
$lignes = $pdo->query($sqlDangereux)->fetchAll(PDO::FETCH_ASSOC);

echo PHP_EOL . '  Requête CONCATÉNÉE : ' . count($lignes) . ' résultat(s) 💥' . PHP_EOL;
echo '    SQL réellement exécuté :' . PHP_EOL;
echo '    ' . $sqlDangereux . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    Décomposons :

      WHERE name = '' OR 1=1 --'
                    │   │    │
                    │   │    └─ « -- » met la fin de la requête en commentaire
                    │   └────── condition toujours vraie → toutes les lignes
                    └────────── la quote ferme la chaîne prévue par le développeur

    Avec un DELETE ou un UPDATE à la place du SELECT, c'était la fin de
    la base de données. 👉 Requête préparée. Toujours.

💡 CE QUE VOUS AVEZ APPRIS

   Un manager regroupe TOUTES les requêtes d'une table au même endroit.
   Il reçoit sa connexion PDO par le constructeur, et renvoie des OBJETS
   plutôt que des tableaux.

   Le reste de votre application ne voit plus jamais de SQL, ni de clés
   de tableau : elle manipule des User, avec l'autocomplétion de
   l'éditeur et la vérification des types.

TXT;
