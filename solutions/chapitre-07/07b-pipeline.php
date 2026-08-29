<?php
/**
 * Corrigé 7.B — La chaîne de fabrication
 * Callables de première classe (PHP 8.1), chaînage fluide, Closure.
 */

declare(strict_types=1);

class Pipeline
{
    /** @var \Closure[] */
    private array $etapes = [];

    /**
     * Renvoie $this : c'est ce qui permet le CHAÎNAGE FLUIDE
     * ->ajouter(...)->ajouter(...)->ajouter(...)
     */
    public function ajouter(callable $etape): static
    {
        // Closure::fromCallable() normalise tout callable en Closure.
        // (En PHP 8.1+, on peut aussi écrire $etape(...) )
        $this->etapes[] = \Closure::fromCallable($etape);
        return $this;
    }

    public function executer(mixed $valeur): mixed
    {
        foreach ($this->etapes as $etape) {
            $valeur = $etape($valeur);
        }
        return $valeur;
    }

    /** Bonus : voir le détail de chaque transformation. */
    public function executerVerbeux(mixed $valeur): mixed
    {
        echo '   départ : ' . var_export($valeur, true) . PHP_EOL;

        foreach ($this->etapes as $i => $etape) {
            $valeur = $etape($valeur);
            printf("   étape %d → %s%s", $i + 1, var_export($valeur, true), PHP_EOL);
        }

        return $valeur;
    }
}

// ────────────────────── 2. Le nettoyeur de pseudo ──────────────────────

echo '🏭 CHAÎNE 1 : NETTOYAGE DE PSEUDO' . PHP_EOL;

$nettoyeur = (new Pipeline())
    ->ajouter(trim(...))                                  // syntaxe PHP 8.1 !
    ->ajouter(strtolower(...))
    ->ajouter(fn(string $s) => str_replace(' ', '_', $s))
    ->ajouter(fn(string $s) => substr($s, 0, 15));

echo '   Entrée : "   Le Grand Sorcier DU Code   "' . PHP_EOL;
echo '   Sortie : ' . $nettoyeur->executer('   Le Grand Sorcier DU Code   ') . PHP_EOL;

echo PHP_EOL . '   Le détail, étape par étape :' . PHP_EOL;
$nettoyeur->executerVerbeux('   Le Grand Sorcier DU Code   ');

// ────────────────────── 3. Le générateur de cri de guerre ──────────────────────

echo PHP_EOL . '📣 CHAÎNE 2 : CRI DE GUERRE' . PHP_EOL;

$criDeGuerre = (new Pipeline())
    ->ajouter(strtoupper(...))
    ->ajouter(fn(string $s) => $s . ' !!!')
    ->ajouter(fn(string $s) => $s . ' ' . $s);

echo '   ' . $criDeGuerre->executer('pour la horde') . PHP_EOL;

// ───────────────── Pourquoi la syntaxe f(...) est meilleure ─────────────────

echo PHP_EOL . '🎯 POURQUOI strtoupper(...) PLUTÔT QUE \'strtoupper\' ?' . PHP_EOL;
echo <<<'TXT'

   AVANT (PHP < 8.1), on passait le nom sous forme de CHAÎNE :
       $f = 'strtoupper';
       $g = [$objet, 'maMethode'];
       $h = 'MaClasse::maMethodeStatique';

   Problèmes :
     - aucune vérification : une faute de frappe ne se voit qu'à l'exécution ;
     - aucune autocomplétion, aucun « aller à la définition » dans l'éditeur ;
     - un renommage automatique de méthode oublie ces chaînes ;
     - trois syntaxes différentes selon le type de callable.

   DEPUIS PHP 8.1, la syntaxe est unique et VÉRIFIÉE À LA COMPILATION :
       $f = strtoupper(...);
       $g = $objet->maMethode(...);
       $h = MaClasse::maMethodeStatique(...);

   Le nom est résolu tout de suite : une faute de frappe est une erreur
   immédiate, pas un mystère à l'exécution.

TXT;

// Démonstration de la différence
echo '   Preuve :' . PHP_EOL;

try {
    $mauvais = 'strtouper';   // typo dans une chaîne : PHP ne dit rien ici
    echo '   $mauvais = "strtouper";  → aucune erreur à cette ligne' . PHP_EOL;
    $mauvais('test');          // …l'erreur n'arrive QUE maintenant
} catch (\Error $e) {
    echo '   $mauvais("test");        → ' . $e->getMessage() . PHP_EOL;
}

echo '   strtouper(...);          → erreur détectée AVANT même l\'exécution' . PHP_EOL;

// ─────────────────────── 4. Bonus PHP 8.5 : l'opérateur pipe ───────────────────────

echo PHP_EOL . '🚰 BONUS : L\'OPÉRATEUR PIPE (PHP 8.5)' . PHP_EOL;

if (PHP_VERSION_ID >= 80500) {
    // Le code n'est évalué que sur PHP 8.5+ : sinon ce serait une erreur
    // de syntaxe au chargement du fichier. D'où le eval().
    $code = '$r = "   Le Grand Sorcier   " |> trim(...) |> strtolower(...); return $r;';
    echo '   Résultat : ' . eval($code) . PHP_EOL;
} else {
    echo '   PHP ' . PHP_VERSION . ' détecté : l\'opérateur |> arrive en 8.5.' . PHP_EOL;
    echo <<<'TXT'

   La syntaxe sera :

       $resultat = '   Le Grand Sorcier   '
           |> trim(...)
           |> strtolower(...);

   Elle se lit de GAUCHE À DROITE, dans l'ordre des opérations,
   au lieu de l'imbrication classique qui se lit de l'intérieur
   vers l'extérieur :

       strtolower(trim('   Le Grand Sorcier   '))
        ③          ②   ①

   Votre classe Pipeline fait exactement la même chose, à la main.
   C'est d'ailleurs un excellent moyen de comprendre l'intérêt de
   l'opérateur avant même de pouvoir l'utiliser. 🔧

TXT;
}
