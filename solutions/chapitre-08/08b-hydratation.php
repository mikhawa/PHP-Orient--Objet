<?php
/**
 * Corrigé 8.B — Le traducteur snake_case → camelCase
 * L'hydratation décortiquée, plus le chemin inverse (toArray).
 */

declare(strict_types=1);

// ─────────────────── 1. La ligne magique, étape par étape ───────────────────

echo '🔬 1. DÉCORTICAGE DE LA LIGNE D\'HYDRATATION' . PHP_EOL . PHP_EOL;

foreach (['annee_publication', 'est_interdit', 'id', 'note_moyenne_pondereee'] as $clef) {
    $etape1 = ucwords($clef, '_');            // majuscule après chaque _
    $etape2 = str_replace('_', '', $etape1);  // on retire les _
    $etape3 = 'set' . $etape2;                // on préfixe

    printf("   '%s'%s", $clef, PHP_EOL);
    printf("   → ucwords(..., '_')     : '%s'%s", $etape1, PHP_EOL);
    printf("   → str_replace('_', '')  : '%s'%s", $etape2, PHP_EOL);
    printf("   → \"set\" . ...           : '%s'%s%s", $etape3, PHP_EOL, PHP_EOL);
}

// ─────────────────── 2. La classe abstraite ───────────────────

abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        $this->hydrate($tab);
    }

    /**
     * Transforme un tableau associatif (typiquement un fetch PDO)
     * en appels de setters.
     */
    protected function hydrate(array $assoc): void
    {
        foreach ($assoc as $clef => $valeur) {
            $methodeName = 'set' . str_replace('_', '', ucwords((string) $clef, '_'));

            // ⚠️ INDISPENSABLE : sans ce test, une colonne sans setter
            // provoquerait une Error fatale « Call to undefined method ».
            // Un simple ALTER TABLE ADD COLUMN casserait toute l'application.
            if (method_exists($this, $methodeName)) {
                $this->$methodeName($valeur);
            }
        }
    }

    /**
     * 5. LE CHEMIN INVERSE : objet → tableau associatif prêt pour un INSERT.
     */
    public function toArray(): array
    {
        $resultat = [];

        foreach (get_class_methods($this) as $methode) {
            // On ne garde que les getters (get* et is* pour les booléens).
            $prefixe = match (true) {
                str_starts_with($methode, 'get') => 'get',
                str_starts_with($methode, 'is')  => 'is',
                default => null,
            };

            if ($prefixe === null || $methode === 'getIterator') {
                continue;
            }

            $nom = substr($methode, strlen($prefixe));   // getAnneePublication → AnneePublication

            // AnneePublication → annee_publication
            // (?<!^) : « pas en début de chaîne », pour éviter '_annee_publication'
            $colonne = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $nom));

            $resultat[$colonne] = $this->$methode();
        }

        return $resultat;
    }
}

// ─────────────────── 3. Livre sans constructeur ───────────────────

class Livre extends AbstractModel
{
    private ?int $id = null;
    private string $titre = '';
    private string $auteur = '';
    private int $anneePublication = 0;
    private bool $estInterdit = false;
    private ?float $noteMoyenne = null;

    // Aucun constructeur : celui d'AbstractModel fait tout le travail.

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getAuteur(): string { return $this->auteur; }
    public function getAnneePublication(): int { return $this->anneePublication; }
    public function isEstInterdit(): bool { return $this->estInterdit; }
    public function getNoteMoyenne(): ?float { return $this->noteMoyenne; }

    public function setId(int|string|null $id): void
    {
        $this->id = $id === null ? null : (int) $id;
    }

    public function setTitre(string $titre): void
    {
        $propre = trim(strip_tags($titre));
        if ($propre === '') {
            throw new InvalidArgumentException('Le titre ne peut pas être vide.');
        }
        $this->titre = $propre;
    }

    public function setAuteur(string $auteur): void
    {
        $this->auteur = trim(strip_tags($auteur));
    }

    public function setAnneePublication(int|string $annee): void
    {
        $this->anneePublication = (int) $annee;
    }

    public function setEstInterdit(bool|int|string $v): void
    {
        $this->estInterdit = (bool) (int) $v;
    }

    public function setNoteMoyenne(float|int|string|null $note): void
    {
        $this->noteMoyenne = ($note === null || $note === '')
            ? null
            : max(0.0, min(10.0, (float) $note));
    }
}

// ─────────────────── 3. L'hydratation en action ───────────────────

echo '✨ 3. HYDRATATION AUTOMATIQUE' . PHP_EOL . PHP_EOL;

// Exactement ce que renvoie $stmt->fetch(PDO::FETCH_ASSOC)
$ligneSql = [
    'id'                => '1',
    'titre'             => 'Dune',
    'auteur'            => 'Frank Herbert',
    'annee_publication' => '1965',
    'est_interdit'      => '0',
    'note_moyenne'      => '9.2',
];

$livre = new Livre($ligneSql);

printf("   getTitre()            : %s%s", $livre->getTitre(), PHP_EOL);
printf("   getAnneePublication() : %d  ← via setAnneePublication()%s",
    $livre->getAnneePublication(), PHP_EOL);
printf("   getNoteMoyenne()      : %s (float, plus une string)%s",
    var_export($livre->getNoteMoyenne(), true), PHP_EOL);

// ─────────────────── 4. Le piège de la colonne fantôme ───────────────────

echo PHP_EOL . '👻 4. UNE COLONNE SANS SETTER' . PHP_EOL . PHP_EOL;

$avecFantome = $ligneSql + ['colonne_fantome' => 'boo', 'created_at' => '2026-01-01'];
$livre2 = new Livre($avecFantome);

echo '   Tableau contenant colonne_fantome et created_at → aucune erreur.' . PHP_EOL;
echo '   hydrate() a calculé setColonneFantome() et setCreatedAt(),' . PHP_EOL;
echo '   ne les a pas trouvées, et est simplement passé à la suite.' . PHP_EOL;

echo PHP_EOL . '   Sans le method_exists(), on aurait :' . PHP_EOL;

try {
    $objet = new Livre();
    $methode = 'setColonneFantome';
    $objet->$methode('boo');
} catch (\Error $e) {
    echo '   → ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . '   💡 C\'est ce qui rend l\'hydratation ROBUSTE : la base peut' . PHP_EOL;
echo '      gagner des colonnes sans casser le code PHP. On choisit ce' . PHP_EOL;
echo '      qu\'on veut mapper en déclarant (ou pas) le setter.' . PHP_EOL;

// ─────────────────── 5. Le chemin inverse : toArray() ───────────────────

echo PHP_EOL . '🔄 5. LE CHEMIN INVERSE : toArray()' . PHP_EOL . PHP_EOL;

$dune = new Livre([
    'titre' => 'Dune',
    'auteur' => 'Frank Herbert',
    'annee_publication' => 1965,
    'note_moyenne' => 9.2,
]);

print_r($dune->toArray());

echo PHP_EOL . '   Ce tableau est directement utilisable :' . PHP_EOL;
echo <<<'TXT'

       $colonnes = array_keys($livre->toArray());
       $sql = 'INSERT INTO livre (' . implode(', ', $colonnes) . ')
               VALUES (:' . implode(', :', $colonnes) . ')';
       $stmt = $pdo->prepare($sql);
       $stmt->execute($livre->toArray());

   ⚠️ Les noms de colonnes viennent de VOS getters, jamais d'une saisie
      utilisateur : ils ne peuvent donc pas servir de vecteur d'injection.
      Ne construisez JAMAIS une requête ainsi à partir de $_POST !

TXT;
