<?php
/**
 * Corrigé 2.G — Quiz exécutable
 *
 * Les extraits 1, 3, 4 et 5 provoquent des erreurs FATALES : impossible de
 * les mettre dans un même fichier (PHP refuserait de le charger). On lance
 * donc chaque cas dans un sous-processus isolé.
 *
 * Explications détaillées dans 02g-quiz-corrige.md
 */

declare(strict_types=1);

$extraits = [
    '1. Instancier une classe abstraite' => <<<'CODE'
        abstract class A { abstract public function go(): string; }
        $a = new A();
        echo "aucune erreur ?!";
        CODE,

    '2. Appeler une méthode protected depuis l\'enfant' => <<<'CODE'
        class B { protected function secret(): string { return 'chut'; } }
        class C extends B { public function espionner(): string { return $this->secret(); } }
        echo (new C())->espionner();
        CODE,

    '3. Réduire la visibilité en redéfinissant' => <<<'CODE'
        class D { public function hello(): string { return 'bonjour'; } }
        class E extends D { protected function hello(): string { return 'salut'; } }
        echo "aucune erreur ?!";
        CODE,

    '4. Hériter d\'une classe final' => <<<'CODE'
        final class F {}
        class G extends F {}
        echo "aucune erreur ?!";
        CODE,

    '5. Lire une propriété private du parent' => <<<'CODE'
        class H { private string $tresor = 'or'; }
        class I extends H { public function voler(): string { return $this->tresor; } }
        echo (new I())->voler();
        CODE,
];

foreach ($extraits as $titre => $code) {
    echo "=== $titre ===" . PHP_EOL;

    $fichier = tempnam(sys_get_temp_dir(), 'quiz') . '.php';
    file_put_contents($fichier, "<?php\n" . $code . "\n");

    // 2>&1 pour capturer aussi les erreurs fatales
    $sortie = shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($fichier) . ' 2>&1');
    unlink($fichier);

    $sortie = trim((string) $sortie);

    // On ne garde que la première ligne utile de l'erreur
    $premiereLigne = strtok($sortie, "\n");

    if (str_contains($sortie, 'error')) {
        echo '  ❌ ' . preg_replace('/ in \/.*$/', '', $premiereLigne) . PHP_EOL;
    } else {
        echo '  ✅ Fonctionne, affiche : ' . $sortie . PHP_EOL;
    }
    echo PHP_EOL;
}

// ------------------------------ BONUS : la propriété fantôme (extrait 5 approfondi)

echo "=== BONUS : ce qui se passe VRAIMENT en écriture ===" . PHP_EOL;

class Parent2 { private string $tresor = 'or'; public function voir(): string { return $this->tresor; } }
class Enfant2 extends Parent2 {
    public function voler(): void {
        // L'enfant ne voit PAS la propriété privée du parent :
        // cette ligne CRÉE une propriété dynamique distincte (Deprecated en 8.2+).
        @$this->tresor = 'volé';
    }
}

$e = new Enfant2();
$e->voler();

echo "  Le parent voit toujours : " . $e->voir() . PHP_EOL;
echo "  Contenu réel de l'objet  : ";
print_r(array_map(
    fn($v) => $v,
    // Le cast en tableau révèle les deux propriétés : celle du parent
    // (préfixée par le nom de classe) et la copie fantôme de l'enfant.
    array_combine(
        array_map(fn($k) => str_replace("\0", '·', $k), array_keys((array) $e)),
        array_values((array) $e)
    )
));
echo "  → deux propriétés du même nom coexistent : le bug parfait. 👻" . PHP_EOL . PHP_EOL;

echo <<<'TXT'
    💡 Le seul extrait qui fonctionne est le n°2 : protected est fait pour
       être utilisé par les enfants. Les quatre autres illustrent les garde-fous
       de PHP : abstraite non instanciable, visibilité jamais réduite,
       final infranchissable, private réservé à sa classe.
    TXT . PHP_EOL;
