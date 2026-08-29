<?php
namespace App\Service\Version1;

class Calculatrice
{
    public function diviser(float $a, float $b): float
    {
        if ($b == 0.0) {
            // Le \ initial : « la classe Exception de la RACINE ».
            throw new \InvalidArgumentException('Division par zéro !');
        }
        return $a / $b;
    }
}

try {
    echo (new Calculatrice())->diviser(10, 0);
} catch (\InvalidArgumentException $e) { // le \ ici aussi, sinon rien n'est attrapé !
    echo '  ✅ Attrapée : ' . $e->getMessage() . \PHP_EOL;
}
