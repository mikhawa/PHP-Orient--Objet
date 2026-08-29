<?php
/**
 * Corrigé 5.A — Point d'entrée du grimoire.
 *
 * UN SEUL require dans tout le projet : celui de l'autoloader.
 * Les trois classes se chargent toutes seules, au moment exact où on les utilise.
 */

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

use App\Model\Grimoire;
use App\Model\Sort;
use App\Service\Incantation;

$grimoire = new Grimoire();
$grimoire->ajouter(new Sort('Boule de feu', 30));
$grimoire->ajouter(new Sort('Bouclier de glace', 20));
$grimoire->ajouter(new Sort('Invocation de gaufre', 5));

echo "📜 Le grimoire contient {$grimoire->nombreDeSorts()} sorts." . PHP_EOL . PHP_EOL;

$incantation = new Incantation();

foreach ($grimoire->getSorts() as $sort) {
    echo '  ' . $incantation->lancer($sort) . PHP_EOL;
}

echo PHP_EOL . "✅ Aucun require en dehors de l'autoloader !" . PHP_EOL;
