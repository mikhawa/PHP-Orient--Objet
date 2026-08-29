<?php
/**
 * Corrigé 5.B — L'espion de l'autoloader
 *
 * Démonstration du chargement PARESSEUX (lazy loading) : une classe n'est
 * chargée qu'au moment précis où PHP en a besoin — jamais avant.
 */

declare(strict_types=1);

// ─────────────────── Un autoloader avec mouchard ───────────────────

spl_autoload_register(function (string $class): void {
    $chemin = __DIR__ . '/grimoire/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($chemin)) {
        echo "🔎 Chargement de $class..." . PHP_EOL;
        require_once $chemin;
    }
});

// ─────────────────── Le scénario ───────────────────

echo "-- début du script --" . PHP_EOL;

$grimoire = new App\Model\Grimoire();
echo "-- grimoire créé --" . PHP_EOL;

// Le corps du if n'est JAMAIS exécuté : PHP ne rencontre jamais
// la classe Incantation, donc l'autoloader n'est jamais appelé pour elle.
if (false) {
    $jamais = new App\Service\Incantation();
}
echo "-- fin du script --" . PHP_EOL;

// ─────────────────── Les réponses ───────────────────

echo PHP_EOL . "═══ 3. LES RÉPONSES ═══" . PHP_EOL;

echo PHP_EOL . "Incantation a-t-elle été chargée ?" . PHP_EOL;
echo '  → ' . (class_exists('App\Service\Incantation', autoload: false)
    ? 'OUI (inattendu !)'
    : 'NON — aucun 🔎 ne la concernant n\'est apparu.') . PHP_EOL;

echo PHP_EOL . 'Et Sort ? Elle n\'apparaît pas non plus : nous n\'avons créé' . PHP_EOL;
echo '  aucun Sort. Seule Grimoire a été chargée.' . PHP_EOL;

// Preuve : maintenant qu'on la mentionne, elle se charge.
echo PHP_EOL . "Mentionnons Incantation maintenant :" . PHP_EOL;
$incantation = new App\Service\Incantation();
echo '  → chargée à la demande, pas une milliseconde avant.' . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    💡 POURQUOI C'EST UNE EXCELLENTE NOUVELLE

       Un projet Symfony ou Laravel comporte des MILLIERS de classes
       (vendor/ en contient facilement 5 000). Sans autoload paresseux, il
       faudrait toutes les inclure à chaque requête HTTP : lecture disque,
       parsing, compilation… plusieurs centaines de millisecondes gaspillées
       pour du code que la page n'utilisera pas.

       Avec l'autoload, une page qui utilise 20 classes en charge exactement
       20. Le reste du framework reste sur le disque, tranquille.

    🧹 ET APRÈS ? Retirez le echo du mouchard. Un autoloader qui affiche
       quelque chose casserait tout site web (impossible d'envoyer un
       en-tête HTTP après le moindre caractère affiché) et polluerait
       toute sortie JSON ou CSV. C'est un outil de découverte, pas de
       production.
    TXT . PHP_EOL;
