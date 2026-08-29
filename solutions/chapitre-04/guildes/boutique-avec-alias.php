<?php
/**
 * Suite du corrigé 4.A — la version avec alias.
 * Les `use` doivent figurer en haut d'un fichier : d'où ce second fichier.
 */

use Guildes\Mages\Potion as PotionDeMana;
use Guildes\Alchimistes\Potion as PotionInstable;

echo "  Exécution réelle :" . PHP_EOL;

$a = new PotionDeMana('Élixir de sagesse', 40);
$b = new PotionInstable('Mixture douteuse', 75);

echo '    ' . $a->boire() . PHP_EOL;
echo '    ' . $b->boire() . PHP_EOL;
