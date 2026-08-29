<?php
namespace App\Service\Version2;

// On importe la classe native une bonne fois pour toutes, en haut du fichier.
use InvalidArgumentException;

class Calculatrice
{
    public function diviser(float $a, float $b): float
    {
        if ($b == 0.0) {
            // Plus besoin de backslash : l'import a fait le travail.
            throw new InvalidArgumentException('Division par zéro !');
        }
        return $a / $b;
    }
}

try {
    echo (new Calculatrice())->diviser(10, 0);
} catch (InvalidArgumentException $e) {
    echo '  ✅ Attrapée : ' . $e->getMessage() . PHP_EOL;
}
