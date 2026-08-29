<?php
/**
 * Corrigé 4.A — Les deux guildes
 *
 * Deux classes portent le MÊME nom (Potion). Sans namespace, PHP refuserait
 * de charger le second fichier : "Cannot declare class Potion, because the
 * name is already in use". Avec les namespaces, elles coexistent sans problème.
 */

declare(strict_types=1);

// L'autoload, c'est le chapitre 5 : ici on inclut à la main.
require_once __DIR__ . '/Mages/Potion.php';
require_once __DIR__ . '/Alchimistes/Potion.php';

// ----------------------------------- 4a. Les noms pleinement qualifiés

echo "═══ Méthode 1 : noms pleinement qualifiés ═══" . PHP_EOL;

// Le \ initial signifie « depuis la racine des namespaces ».
$potionMage = new \Guildes\Mages\Potion('Élixir de sagesse', 40);
$potionAlchimiste = new \Guildes\Alchimistes\Potion('Mixture douteuse', 75);

echo '  ' . $potionMage->nom . ' : ' . $potionMage->boire() . PHP_EOL;
echo '  ' . $potionAlchimiste->nom . ' : ' . $potionAlchimiste->boire() . PHP_EOL;

// -------------------------------------------------- 4b. Les alias avec `as`

// `use` s'écrit normalement en HAUT du fichier (après namespace/declare).
// Il est placé ici uniquement pour la lisibilité pédagogique.
// ⚠️ `use` en haut d'un fichier = importation de namespace.
//    `use` dans une classe      = utilisation d'un TRAIT. Rien à voir !

echo PHP_EOL . "═══ Méthode 2 : alias avec `as` ═══" . PHP_EOL;

echo <<<'TXT'
  En haut du fichier, on aurait écrit :

    use Guildes\Mages\Potion as PotionDeMana;
    use Guildes\Alchimistes\Potion as PotionInstable;

  Puis, dans le code, les noms courts suffisent :

    $a = new PotionDeMana('Élixir', 40);
    $b = new PotionInstable('Mixture', 75);

  Sans les alias, les deux `use` provoqueraient une erreur fatale :
  "Cannot use Guildes\Alchimistes\Potion as Potion because the name
   is already in use".

TXT;

// Démonstration réelle via un fichier séparé (les `use` doivent être en haut)
require __DIR__ . '/boutique-avec-alias.php';

// -------------------------------------------- Bonus : constantes de namespace

echo PHP_EOL . "═══ Bonus : les constantes de namespace ═══" . PHP_EOL;
echo '  Mages       : ' . \Guildes\Mages\DEVISE . PHP_EOL;
echo '  Alchimistes : ' . \Guildes\Alchimistes\DEVISE . PHP_EOL;
echo PHP_EOL . "  ⚠️ Piège : une constante de namespace s'écrit \\Guildes\\Mages\\DEVISE," . PHP_EOL;
echo "     PAS Guildes\\Mages::DEVISE (ça, c'est une constante de CLASSE)." . PHP_EOL;
