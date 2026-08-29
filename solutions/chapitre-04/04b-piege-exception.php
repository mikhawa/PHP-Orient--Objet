<?php
/**
 * Corrigé 4.B — Le piège de l'Exception
 *
 * ═══════════════════════ LES RÉPONSES ═══════════════════════
 *
 * 1. PHP cherche la classe App\Service\Exception — et pas Exception !
 *    Message : « Class "App\Service\Exception" not found ».
 *
 *    Pourquoi ? Dans un fichier avec `namespace App\Service;`, TOUT nom de
 *    classe non préfixé est résolu RELATIVEMENT au namespace courant.
 *    `new Exception(...)` signifie donc `new \App\Service\Exception(...)`.
 *
 * 2. Les classes natives de PHP (Exception, PDO, DateTime, ArrayObject…)
 *    vivent dans le namespace GLOBAL, c'est-à-dire à la racine `\`.
 *    Depuis un namespace, il faut donc les désigner explicitement.
 *
 *    ⚠️ Subtilité : cette résolution automatique ne s'applique qu'aux CLASSES.
 *    Les FONCTIONS et CONSTANTES natives (strlen, count, PHP_EOL…) bénéficient
 *    d'un repli automatique vers le global — c'est pour cela que strlen()
 *    fonctionne sans backslash, mais pas Exception.
 *
 * 4. Le catch non corrigé n'attraperait JAMAIS rien : `catch (Exception $e)`
 *    signifie `catch (\App\Service\Exception $e)`. Même si le throw
 *    fonctionnait et lançait une vraie \Exception, le catch ne
 *    correspondrait pas — l'exception remonterait non attrapée.
 *    Deux bugs pour le prix d'un. 🎁
 */

declare(strict_types=1);

// ══════════════════════ DÉMONSTRATION DU BUG ══════════════════════

echo "═══ 1. Le code buggé ═══" . PHP_EOL;

$codeBugge = <<<'CODE'
<?php
namespace App\Service;

class Calculatrice {
    public function diviser(float $a, float $b): float {
        if ($b == 0.0) {
            throw new Exception('Division par zéro !');
        }
        return $a / $b;
    }
}

try {
    echo (new Calculatrice())->diviser(10, 0);
} catch (Exception $e) {
    echo 'Attrapée : ' . $e->getMessage();
}
CODE;

$fichier = tempnam(sys_get_temp_dir(), 'ns') . '.php';
file_put_contents($fichier, $codeBugge);
$sortie = shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($fichier) . ' 2>&1');
unlink($fichier);
echo '  ' . preg_replace('/ in \/.*$/m', '', trim(strtok((string) $sortie, "\n"))) . PHP_EOL;
echo '  → PHP cherche App\Service\Exception, qui n\'existe pas.' . PHP_EOL;

// ══════════════════════ CORRECTION 1 : le préfixe \ ══════════════════════

echo PHP_EOL . "═══ 3a. Correction par le préfixe \\ ═══" . PHP_EOL;
require __DIR__ . '/04b-correction-backslash.php';

// ══════════════════════ CORRECTION 2 : l'importation use ══════════════════

echo PHP_EOL . "═══ 3b. Correction par `use` ═══" . PHP_EOL;
require __DIR__ . '/04b-correction-use.php';

echo PHP_EOL . <<<'TXT'
    💡 RÈGLE À RETENIR : dans un fichier avec un namespace, préfixez toujours
       les classes natives d'un backslash — \Exception, \PDO, \DateTimeImmutable —
       ou importez-les en haut du fichier avec `use Exception;`.

       Le jour où vous verrez « Class "App\Truc\PDO" not found », vous saurez
       exactement quoi faire. 🔧
    TXT . PHP_EOL;
