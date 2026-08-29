<?php
/**
 * Corrigé 8.C — Le paquet de cartes
 * Enums + readonly + conversion SQL ↔ enum dans un setter.
 */

declare(strict_types=1);

enum Couleur: string
{
    case Pique = 'pique';
    case Coeur = 'coeur';
    case Carreau = 'carreau';
    case Trefle = 'trefle';

    public function emoji(): string
    {
        return match ($this) {
            Couleur::Pique   => '♠️',
            Couleur::Coeur   => '♥️',
            Couleur::Carreau => '♦️',
            Couleur::Trefle  => '♣️',
        };
    }

    public function libelle(): string
    {
        return match ($this) {
            Couleur::Pique   => 'Pique',
            Couleur::Coeur   => 'Cœur',
            Couleur::Carreau => 'Carreau',
            Couleur::Trefle  => 'Trèfle',
        };
    }
}

enum Valeur: int
{
    case Deux = 2;
    case Trois = 3;
    case Quatre = 4;
    case Cinq = 5;
    case Six = 6;
    case Sept = 7;
    case Huit = 8;
    case Neuf = 9;
    case Dix = 10;
    case Valet = 11;
    case Dame = 12;
    case Roi = 13;
    case As = 14;

    public function libelle(): string
    {
        return match ($this) {
            Valeur::Valet => 'Valet',
            Valeur::Dame  => 'Dame',
            Valeur::Roi   => 'Roi',
            Valeur::As    => 'As',
            // Toutes les autres : leur valeur numérique.
            default => (string) $this->value,
        };
    }
}

// Une carte est immuable : readonly class (PHP 8.2).
readonly class Carte
{
    public function __construct(
        public Couleur $couleur,
        public Valeur $valeur,
    ) {}

    public function __toString(): string
    {
        return sprintf(
            '%s %s de %s',
            $this->couleur->emoji(),
            $this->valeur->libelle(),
            $this->couleur->libelle()
        );
    }
}

class Paquet
{
    /** @var Carte[] */
    private array $cartes = [];

    public function __construct()
    {
        // 4 couleurs × 13 valeurs = 52 cartes, en 4 lignes.
        foreach (Couleur::cases() as $couleur) {
            foreach (Valeur::cases() as $valeur) {
                $this->cartes[] = new Carte($couleur, $valeur);
            }
        }
    }

    public function melanger(): void
    {
        shuffle($this->cartes);
    }

    public function piocher(): ?Carte
    {
        return array_pop($this->cartes);   // null si le paquet est vide
    }

    public function nombreDeCartesRestantes(): int
    {
        return count($this->cartes);
    }
}

// ─────────────────────────── Le paquet en action ───────────────────────────

$paquet = new Paquet();
echo "🃏 Paquet créé : {$paquet->nombreDeCartesRestantes()} cartes." . PHP_EOL;

$paquet->melanger();
echo '   Trois cartes au hasard :' . PHP_EOL;
for ($i = 0; $i < 3; $i++) {
    echo '     ' . $paquet->piocher() . PHP_EOL;
}
echo "   Reste : {$paquet->nombreDeCartesRestantes()} cartes." . PHP_EOL;

// ─────────────────────── 5. La bataille en 10 manches ───────────────────────

echo PHP_EOL . '⚔️  LA BATAILLE — 10 manches' . PHP_EOL . PHP_EOL;

$paquetBataille = new Paquet();
$paquetBataille->melanger();

$scoreA = $scoreB = 0;

for ($manche = 1; $manche <= 10; $manche++) {
    $carteA = $paquetBataille->piocher();
    $carteB = $paquetBataille->piocher();

    // Les enums adossés se comparent par ->value : pas besoin de table
    // de correspondance, l'ordre est encodé dans l'enum lui-même.
    $verdict = match (true) {
        $carteA->valeur->value > $carteB->valeur->value => '← Joueur A',
        $carteB->valeur->value > $carteA->valeur->value => '→ Joueur B',
        default => '= bataille !',
    };

    $carteA->valeur->value > $carteB->valeur->value
        ? $scoreA++
        : ($carteB->valeur->value > $carteA->valeur->value ? $scoreB++ : null);

    printf("   Manche %2d : %-22s vs %-22s %s%s",
        $manche, (string) $carteA, (string) $carteB, $verdict, PHP_EOL);
}

printf("%s   🏆 Score : Joueur A %d — %d Joueur B%s", PHP_EOL, $scoreA, $scoreB, PHP_EOL);
printf("   (%d cartes restantes dans le paquet)%s",
    $paquetBataille->nombreDeCartesRestantes(), PHP_EOL);

// ─────────────── 4. Le setter qui convertit SQL → enum ───────────────

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

class CartePersistee extends AbstractModel
{
    private ?int $id = null;
    private Couleur $couleur = Couleur::Pique;
    private Valeur $valeur = Valeur::Deux;

    public function setId(int|string|null $id): void
    {
        $this->id = $id === null ? null : (int) $id;
    }

    /**
     * LE SETTER CLÉ : il reçoit une chaîne (colonne SQL) et sort un enum.
     * C'est la frontière entre le monde SQL et le monde objet.
     */
    public function setCouleur(Couleur|string $valeur): void
    {
        if ($valeur instanceof Couleur) {
            $this->couleur = $valeur;
            return;
        }

        // BLINDAGE (réponse au bonus) : la base peut contenir des espaces
        // parasites ou une casse inattendue. On normalise AVANT from().
        $normalisee = strtolower(trim($valeur));

        $this->couleur = Couleur::tryFrom($normalisee)
            ?? throw new InvalidArgumentException(
                "Couleur inconnue en base : '$valeur' "
                . '(attendu : ' . implode(', ', array_column(Couleur::cases(), 'value')) . ')'
            );
    }

    public function setValeur(Valeur|int|string $valeur): void
    {
        $this->valeur = $valeur instanceof Valeur
            ? $valeur
            : (Valeur::tryFrom((int) $valeur)
                ?? throw new InvalidArgumentException("Valeur inconnue : '$valeur'"));
    }

    public function getCouleur(): Couleur { return $this->couleur; }
    public function getValeur(): Valeur { return $this->valeur; }

    /** Le chemin retour : enum → scalaire, pour l'INSERT. */
    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'couleur' => $this->couleur->value,   // ->value, pas l'enum !
            'valeur'  => $this->valeur->value,
        ];
    }
}

echo PHP_EOL . '💾 4. CONVERSION SQL ↔ ENUM' . PHP_EOL . PHP_EOL;

$ligneSql = ['id' => '7', 'couleur' => 'coeur', 'valeur' => '12'];
$carte = new CartePersistee($ligneSql);

printf("   Ligne SQL : couleur='coeur' (string), valeur='12' (string)%s", PHP_EOL);
printf("   Après hydratation : %s %s de %s%s",
    $carte->getCouleur()->emoji(),
    $carte->getValeur()->libelle(),
    $carte->getCouleur()->libelle(),
    PHP_EOL
);
echo '   Retour vers SQL : ';
print_r($carte->toArray());

// ─────────────── Bonus : la donnée sale ───────────────

echo PHP_EOL . '🧼 BONUS : ET SI LA BASE CONTIENT \'PIQUE \' (espace + majuscules) ?' . PHP_EOL . PHP_EOL;

$sale = new CartePersistee(['couleur' => 'PIQUE ', 'valeur' => 14]);
printf("   'PIQUE ' → %s ✅ (trim + strtolower avant tryFrom)%s",
    $sale->getCouleur()->libelle(), PHP_EOL);

try {
    new CartePersistee(['couleur' => 'losange', 'valeur' => 5]);
} catch (InvalidArgumentException $e) {
    echo '   ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . <<<'TXT'
    💡 from() ET LE ?? throw : UN IDIOME PHP 8 BIEN PRATIQUE

         $this->couleur = Couleur::tryFrom($v)
             ?? throw new InvalidArgumentException("...");

       Depuis PHP 8.0, `throw` est une EXPRESSION : on peut l'utiliser à
       droite d'un ??, d'un ?:, dans une fonction fléchée… Résultat :
       « prends la valeur, ou explose avec un message utile », en une ligne.

    ⚠️ POURQUOI NE PAS METTRE L'ENUM DIRECTEMENT EN BASE ?
       Une colonne SQL stocke des scalaires. On y met donc ->value, et on
       reconstruit l'enum au chargement. C'est précisément le rôle des
       setters : garder le monde objet propre, quelles que soient les
       contorsions du monde relationnel. 🌉
    TXT . PHP_EOL;
