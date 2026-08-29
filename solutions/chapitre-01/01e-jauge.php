<?php
/**
 * Corrigé 1.E — La jauge magique
 * Property hooks + visibilité asymétrique (PHP 8.4).
 * ⚠️ Nécessite PHP 8.4 ou plus : vérifiez avec `php -v`.
 */

declare(strict_types=1);

if (PHP_VERSION_ID < 80400) {
    exit("Ce corrigé nécessite PHP 8.4 (property hooks). Version détectée : " . PHP_VERSION . PHP_EOL);
}

class Jauge
{
    // Hook `set` : intercepte CHAQUE écriture. Plus besoin de setValeur() !
    // De l'extérieur, on écrit $jauge->valeur = 130; comme une propriété normale.
    public int $valeur = 0 {
        set(int $valeur) {
            $this->valeur = max(0, min($this->max, $valeur));
            $this->nbModifications++;
        }
    }

    // Propriété VIRTUELLE : calculée à la lecture, jamais stockée en mémoire.
    public float $pourcentage {
        get => $this->max === 0 ? 0.0 : round($this->valeur / $this->max * 100, 1);
    }

    // Visibilité asymétrique : lisible par tous, modifiable seulement ici.
    public private(set) int $nbModifications = 0;

    public function __construct(
        public readonly string $nom,
        public readonly int $max = 100,
    ) {
        $this->valeur = $max; // passe par le hook set
        $this->nbModifications = 0; // on ne compte pas l'initialisation
    }

    public function barre(int $cases = 10): string
    {
        $pleines = (int) round($this->pourcentage / 100 * $cases);

        return sprintf(
            '%s [%s%s] %s %%',
            $this->nom,
            str_repeat('█', $pleines),
            str_repeat('░', $cases - $pleines),
            $this->pourcentage
        );
    }
}

// ---------------------------------------------------------------- Utilisation

$vie = new Jauge('Vie', 100);

echo $vie->barre() . PHP_EOL;

echo PHP_EOL . "Tentative de tricher en mettant 130 :" . PHP_EOL;
$vie->valeur = 130;                    // le hook borne à 100
echo '  → ' . $vie->barre() . PHP_EOL;

echo PHP_EOL . "Le dragon inflige 65 dégâts :" . PHP_EOL;
$vie->valeur -= 65;
echo '  → ' . $vie->barre() . PHP_EOL;
echo '  → pourcentage (propriété virtuelle) : ' . $vie->pourcentage . PHP_EOL;

echo PHP_EOL . "Tentative de descendre sous zéro :" . PHP_EOL;
$vie->valeur = -9999;
echo '  → ' . $vie->barre() . PHP_EOL;

// ------------------------------------------------- Visibilité asymétrique

echo PHP_EOL . "🔢 Nombre de modifications (lecture publique) : {$vie->nbModifications}" . PHP_EOL;

try {
    $vie->nbModifications = 0; // interdit depuis l'extérieur
} catch (Error $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}

// -------------------------------------------------------- Autres usages

echo PHP_EOL . "Une jauge, ça sert à tout :" . PHP_EOL;

$mana = new Jauge('Mana', 50);
$mana->valeur -= 30;
echo '  ' . $mana->barre() . PHP_EOL;

$batterie = new Jauge('Batterie', 100);
$batterie->valeur = 7;
echo '  ' . $batterie->barre() . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    💡 À retenir : le hook `set` fait le travail d'un setter, mais l'appelant
       écrit une propriété normale. Le hook `get` crée une valeur calculée sans
       la stocker. Et private(set) donne une propriété publique en lecture,
       verrouillée en écriture — le tout sans écrire un seul getter.
    TXT . PHP_EOL;
