<?php
/**
 * Corrigé 8.1 à 8.6 — De la ligne SQL à l'objet, étape par étape
 */

declare(strict_types=1);

// ─────────────────── 8.5 : la classe qui hydrate ───────────────────

abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        foreach ($tab as $clef => $valeur) {
            // 'name' devient 'setName'
            $methode = 'set' . ucfirst((string) $clef);

            // 8.6 : LE test indispensable
            if (method_exists($this, $methode)) {
                $this->$methode($valeur);
            }
        }
    }
}

class User extends AbstractModel
{
    // 8.3 : propriétés PRIVÉES
    private int $id = 0;
    private string $name = '';
    private string $email = '';

    // 8.5 : plus de constructeur ! Celui d'AbstractModel fait tout.

    // ── Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }

    // ── Setters
    public function setId(int|string $id): void
    {
        $this->id = (int) $id;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    // 8.3 : le setter qui VÉRIFIE
    public function setEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Email invalide : ' . $email);
        }

        $this->email = $email;
    }
}

// ═══════════════════════ 8.1 et 8.2 ═══════════════════════

echo '── 8.1 / 8.2 : une ligne SQL devient un objet ──' . PHP_EOL;

$ligne = ['id' => 1, 'name' => 'Aline', 'email' => 'aline@example.com'];

$user = new User($ligne);
echo '  ' . $user->getName() . ' <' . $user->getEmail() . '>' . PHP_EOL;

// ═══════════════════════ 8.3 : la validation ═══════════════════════

echo PHP_EOL . '── 8.3 : impossible de créer un User invalide ──' . PHP_EOL;

try {
    $mauvais = new User(['id' => 2, 'name' => 'Bob', 'email' => 'pas-un-email']);
} catch (InvalidArgumentException $e) {
    echo '  ⛔ ' . $e->getMessage() . PHP_EOL;
}

// La propriété est privée : on ne peut pas la contourner non plus
try {
    $user->email = 'triche@example.com';
} catch (Error $e) {
    echo '  ⛔ ' . $e->getMessage() . PHP_EOL;
}

// ═══════════════════════ 8.4 : plusieurs lignes ═══════════════════════

echo PHP_EOL . '── 8.4 : plusieurs lignes ──' . PHP_EOL;

$lignes = [
    ['id' => 1, 'name' => 'Aline', 'email' => 'aline@example.com'],
    ['id' => 2, 'name' => 'Bob',   'email' => 'bob@example.com'],
    ['id' => 3, 'name' => 'Carla', 'email' => 'carla@example.com'],
];

$users = [];

foreach ($lignes as $l) {
    $users[] = new User($l);
}

foreach ($users as $u) {
    echo '  - ' . $u->getName() . PHP_EOL;
}

// ═══════════════════════ 8.6 : la colonne sans setter ═══════════════════════

echo PHP_EOL . '── 8.6 : une colonne inconnue ──' . PHP_EOL;

$avecColonneEnPlus = [
    'id' => 4,
    'name' => 'David',
    'email' => 'david@example.com',
    'created_at' => '2026-01-01',    // aucune méthode setCreatedAt()
];

$david = new User($avecColonneEnPlus);

echo '  1. Ça plante ? NON : ' . $david->getName() . ' a bien été créé.' . PHP_EOL;
echo '     hydrate() a cherché setCreatedAt(), ne l\'a pas trouvée,' . PHP_EOL;
echo '     et est passé à la clé suivante.' . PHP_EOL;

echo PHP_EOL . '  2. Sans le method_exists() :' . PHP_EOL;

try {
    $methode = 'setCreatedAt';
    $david->$methode('2026-01-01');
} catch (Error $e) {
    echo '     ⛔ ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . <<<'TXT'
     3. POURQUOI C'EST INDISPENSABLE
        Une base de données évolue : on ajoute une colonne created_at,
        updated_at, un compteur de visites… Sans le method_exists(), le
        moindre ALTER TABLE ferait planter TOUTE l'application.

        Avec lui, vous choisissez ce que vous voulez récupérer, en
        déclarant (ou non) le setter correspondant. Le code PHP et la
        base de données peuvent évoluer chacun à leur rythme.

💡 CE QUE VOUS AVEZ APPRIS

   Une ligne SQL (tableau associatif) devient un OBJET, ce qui apporte :
     - le typage (impossible de mettre un texte dans un id) ;
     - la validation (impossible de créer un User sans email valide) ;
     - des méthodes métier au même endroit que les données.

   L'hydratation automatise la conversion : une boucle, des setters,
   et n'importe quel tableau devient un objet valide.

TXT;
