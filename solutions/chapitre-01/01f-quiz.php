<?php
/**
 * Corrigé 1.F — Quiz exécutable
 * Lancez ce fichier pour voir chaque réponse en direct.
 * Explications détaillées dans 01f-quiz-corrige.md
 */

declare(strict_types=1);

echo "=== Extrait 1 : référence ou copie ? ===" . PHP_EOL;
class Boite { public string $contenu = 'vide'; }
$a = new Boite();
$b = $a;
$b->contenu = 'trésor';
echo "  Réponse : {$a->contenu}  (les objets se passent par référence)" . PHP_EOL;

echo PHP_EOL . "=== Extrait 2 : clone ===" . PHP_EOL;
$c = clone $a;
$c->contenu = 'chaussette';
echo "  Réponse : {$a->contenu}  (clone crée un objet indépendant)" . PHP_EOL;

echo PHP_EOL . "=== Extrait 3 : readonly ===" . PHP_EOL;
class Portefeuille {
    public function __construct(public readonly float $solde) {}
}
$p = new Portefeuille(50.0);
try {
    $p->solde = 1000000.0;
} catch (Error $e) {
    echo "  Réponse : Error → " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== Extrait 4 : propriété statique ===" . PHP_EOL;
class Chrono {
    public static int $tics = 0;
    public function tic(): void { self::$tics++; }
}
$c1 = new Chrono();
$c2 = new Chrono();
$c1->tic();
$c2->tic();
$c2->tic();
echo "  Réponse : " . Chrono::$tics . "  (une seule variable pour toute la classe)" . PHP_EOL;

echo PHP_EOL . "=== Extrait 5 : propriété dynamique (PHP 8.2+) ===" . PHP_EOL;
class Fantome {}
$f = new Fantome();

// On capture le Deprecated pour l'afficher proprement.
set_error_handler(function (int $no, string $msg): bool {
    echo "  Réponse : Deprecated → $msg" . PHP_EOL;
    return true;
});
$f->boo = 'BOUH';
restore_error_handler();

echo "  (la propriété existe quand même : {$f->boo})" . PHP_EOL;

echo PHP_EOL . "=== Bonus : la version autorisée ===" . PHP_EOL;
#[\AllowDynamicProperties]
class SacFourreTout {}
$sac = new SacFourreTout();
$sac->nImporteQuoi = 42;
echo "  Avec #[\\AllowDynamicProperties] : aucun avertissement ({$sac->nImporteQuoi})" . PHP_EOL;
