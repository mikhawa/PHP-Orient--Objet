<?php
/**
 * Corrigé 10 — L'ARÈNE DES HÉROS
 * Point d'entrée : le seul require du projet, puis tout se charge tout seul.
 *
 * Lancement :  php public/index.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

use App\Exception\ArenaVideException;
use App\Manager\HeroManager;
use App\Model\Guerrier;
use App\Model\Hero;
use App\Model\Mage;
use App\Model\Voleur;
use App\Service\Arene;

// ═════════════════════════ Connexion et schéma ═════════════════════════

// SQLite en mémoire : aucun serveur à installer.
// Pour MySQL, remplacez par :
//   new PDO('mysql:host=localhost;dbname=arene;charset=utf8mb4', 'root', '', [...])
$pdo = new PDO('sqlite::memory:', options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('
    CREATE TABLE hero (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        name          VARCHAR(100) NOT NULL,
        classe        VARCHAR(20)  NOT NULL,
        pv            INT NOT NULL DEFAULT 100,
        force_attaque INT NOT NULL DEFAULT 10,
        victoires     INT NOT NULL DEFAULT 0,
        defaites      INT NOT NULL DEFAULT 0
    )
');

$manager = new HeroManager($pdo);
$arene = new Arene($manager);

// ═════════════════════════ Création des héros ═════════════════════════

echo PHP_EOL . '🏟️  BIENVENUE DANS L\'ARÈNE  🏟️' . PHP_EOL . PHP_EOL;

$heros = [
    new Guerrier(['name' => 'Ragnar',   'force_attaque' => 14]),
    new Mage(    ['name' => 'Merlin',   'force_attaque' => 12]),
    new Voleur(  ['name' => 'Shadow',   'force_attaque' => 13]),
    new Guerrier(['name' => 'Brunhild', 'force_attaque' => 15]),
];

echo '── Les combattants du jour ──' . PHP_EOL;

foreach ($heros as $hero) {
    $manager->add($hero);
    printf(
        "   #%d %s — %d PV, force %d%s",
        $hero->getId(), $hero, $hero->getPvMax(), $hero->getForceAttaque(), PHP_EOL
    );
}

// ═════════════════════════ Le tournoi ═════════════════════════

echo PHP_EOL . '── ⚔️  DEMI-FINALE 1 ──' . PHP_EOL;
$finaliste1 = $arene->combat($heros[0], $heros[1]);

echo '── ⚔️  DEMI-FINALE 2 ──' . PHP_EOL;
$finaliste2 = $arene->combat($heros[2], $heros[3]);

echo '── 🏅 FINALE ──' . PHP_EOL;
$champion = $arene->combat($finaliste1, $finaliste2);

printf("👑 CHAMPION DE L'ARÈNE : %s%s%s", $champion, PHP_EOL, PHP_EOL);

// ═════════════════════════ Le classement ═════════════════════════

echo '🏆 CLASSEMENT GÉNÉRAL 🏆' . PHP_EOL . PHP_EOL;

foreach ($manager->getClassement() as $rang => $hero) {
    printf(
        "   %d. %s %d victoire%s, %d défaite%s%s",
        $rang + 1,
        // mb_str_pad (PHP 8.3) compte les CARACTÈRES, pas les octets :
        // indispensable avec des emojis, où %-18s de printf se trompe.
        mb_str_pad((string) $hero, 16),
        $hero->getVictoires(), $hero->getVictoires() > 1 ? 's' : '',
        $hero->getDefaites(),  $hero->getDefaites() > 1 ? 's' : '',
        PHP_EOL
    );
}

// ═══════════════════ Statistiques (visibilité asymétrique) ═══════════════════

echo PHP_EOL . '📊 DÉGÂTS INFLIGÉS PENDANT LE TOURNOI' . PHP_EOL . PHP_EOL;

foreach ($heros as $hero) {
    printf("   %s %4d dégâts au total%s",
        mb_str_pad((string) $hero, 16), $hero->degatsInfligesTotal, PHP_EOL);
}

echo PHP_EOL . '   💡 degatsInfligesTotal est en public private(set) : lisible' . PHP_EOL;
echo '      partout, mais impossible à truquer depuis l\'extérieur.' . PHP_EOL;

// ═════════════════════════ Les exceptions ═════════════════════════

echo PHP_EOL . '🛡️  LES GARDE-FOUS' . PHP_EOL . PHP_EOL;

try {
    $arene->combat($champion, $champion);
} catch (ArenaVideException $e) {
    echo '   ' . $e->getMessage() . PHP_EOL;
}

try {
    new Guerrier(['name' => '   ']);
} catch (InvalidArgumentException $e) {
    echo '   ⛔ ' . $e->getMessage() . PHP_EOL;
}

// ═════════════════════════ Statistiques SQL ═════════════════════════

echo PHP_EOL . '🗃️  VÉRIFICATION EN BASE (les résultats ont bien été persistés)' . PHP_EOL . PHP_EOL;

$stmt = $pdo->query(
    'SELECT classe, COUNT(*) AS effectif, SUM(victoires) AS total_victoires
       FROM hero GROUP BY classe ORDER BY total_victoires DESC'
);

printf("   %-12s %-10s %s%s", 'CLASSE', 'EFFECTIF', 'VICTOIRES', PHP_EOL);
printf("   %s%s", str_repeat('─', 34), PHP_EOL);

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
    printf("   %-12s %-10d %d%s",
        $ligne['classe'], $ligne['effectif'], $ligne['total_victoires'], PHP_EOL);
}

echo PHP_EOL . '✅ Fin du tournoi. Relancez le script : le résultat sera différent !' . PHP_EOL;
