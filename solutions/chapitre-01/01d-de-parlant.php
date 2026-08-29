<?php
/**
 * Corrigé 1.D — Le lanceur de dés parlant
 * readonly, fabriques statiques, __toString().
 */

declare(strict_types=1);

class De
{
    private ?int $dernierLancer = null;

    public function __construct(public readonly int $faces = 6) {}

    public function lancer(): int
    {
        // random_int() est cryptographiquement sûr, contrairement à rand().
        return $this->dernierLancer = random_int(1, $this->faces);
    }

    public function __toString(): string
    {
        return $this->dernierLancer === null
            ? "🎲 Dé à {$this->faces} faces (jamais lancé)"
            : "🎲 Dé à {$this->faces} faces → {$this->dernierLancer}";
    }

    // ------------------------------------------------------ Fabriques statiques
    // Une fabrique est une méthode statique qui construit un objet préconfiguré.
    // Le type de retour `static` (PHP 8.0) = « une instance de la classe appelée ».

    public static function classique(): static
    {
        return new static(6);
    }

    public static function deDonjon(): static
    {
        return new static(20);
    }

    // Bonus : le jet « avec avantage » cher aux rôlistes.
    public function lancerAvantage(): int
    {
        $a = random_int(1, $this->faces);
        $b = random_int(1, $this->faces);
        echo "   (jets : $a et $b → on garde le meilleur)" . PHP_EOL;
        return $this->dernierLancer = max($a, $b);
    }
}

// ---------------------------------------------------------------- Utilisation

$d6 = De::classique();
$d20 = De::deDonjon();

echo $d6 . PHP_EOL;   // jamais lancé
$d6->lancer();
echo $d6 . PHP_EOL;   // → résultat

echo PHP_EOL . "🎯 Jet d'initiative avec avantage :" . PHP_EOL;
echo '   Résultat : ' . $d20->lancerAvantage() . PHP_EOL;

// --------------------------------------------------- Best of 5 : d6 contre d20

echo PHP_EOL . "⚔️  BEST OF 5 : d6 contre d20" . PHP_EOL;

$scoreD6 = $scoreD20 = 0;

for ($manche = 1; $manche <= 5; $manche++) {
    $jetD6 = $d6->lancer();
    $jetD20 = $d20->lancer();

    $verdict = match (true) {
        $jetD6 > $jetD20 => '🎲 le d6 !',
        $jetD20 > $jetD6 => '🎲 le d20 !',
        default          => '🤝 égalité',
    };

    $jetD6 > $jetD20 ? $scoreD6++ : ($jetD20 > $jetD6 ? $scoreD20++ : null);

    printf("   Manche %d : d6=%2d vs d20=%2d → %s%s", $manche, $jetD6, $jetD20, $verdict, PHP_EOL);
}

printf("%s🏆 Score final : d6 %d — %d d20%s", PHP_EOL, $scoreD6, $scoreD20, PHP_EOL);
echo "   (spoiler : sur 20 faces, le d20 sort en moyenne 10,5 contre 3,5 pour le d6…)" . PHP_EOL;
