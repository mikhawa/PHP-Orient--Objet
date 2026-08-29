<?php
/**
 * Corrigé 6.D — Quiz exécutable
 * Explications détaillées dans 06d-quiz-corrige.md
 */

declare(strict_types=1);

echo "=== Scénario 1 : l'ordre des catch ===" . PHP_EOL;
echo '  Affiche : ';
try {
    throw new RuntimeException('A');
} catch (Exception $e) {
    echo '1';
} catch (RuntimeException $e) {
    echo '2';    // ← bloc MORT : jamais atteint
}
echo PHP_EOL . '  → RuntimeException hérite d\'Exception : le premier catch gagne.' . PHP_EOL;

echo PHP_EOL . "=== Scénario 2 : pas d'exception, mais un finally ===" . PHP_EOL;
echo '  Affiche : ';
try {
    echo (int) '12 singes';
} catch (Exception $e) {
    echo 'attrapé';
} finally {
    echo ' / finally';
}
echo PHP_EOL . '  → (int) "12 singes" vaut 12 sans lever d\'exception ;' . PHP_EOL;
echo '    finally s\'exécute de toute façon.' . PHP_EOL;

echo PHP_EOL . "=== Scénario 3 : never + catch sans variable ===" . PHP_EOL;

function boum(): never
{
    throw new LogicException('B');
}

echo '  Affiche : ';
try {
    boum();
    echo 'après boum';       // ← INACCESSIBLE
} catch (LogicException) {   // PHP 8.0 : pas besoin de $e
    echo 'attrapé B';
}
echo PHP_EOL . '  → "après boum" n\'est jamais atteint : throw interrompt le flux.' . PHP_EOL;

// ───────────────────────── Bonus : finally et return ─────────────────────────

echo PHP_EOL . "=== BONUS : finally s'exécute même après un return ===" . PHP_EOL;

function testFinally(): string
{
    try {
        return 'valeur du try';
    } finally {
        // Ce bloc s'exécute APRÈS l'évaluation du return, mais AVANT
        // que la valeur ne soit rendue à l'appelant.
        echo '  (le finally passe par ici !)' . PHP_EOL;
    }
}

echo '  Résultat : ' . testFinally() . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    ⚠️  PIÈGE ULTIME : si le finally contient LUI-MÊME un return, il écrase
       celui du try. À éviter absolument — c'est illisible :

         function piege(): string {
             try    { return 'A'; }
             finally { return 'B'; }   // renvoie 'B' !
         }

       Règle : dans un finally, on nettoie (fermer un fichier, libérer un
       verrou, journaliser). On ne décide pas de la valeur de retour.
    TXT . PHP_EOL;
