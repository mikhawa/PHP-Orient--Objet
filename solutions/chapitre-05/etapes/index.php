<?php
/**
 * Corrigé 5.1 à 5.5 — Le grimoire, étape par étape
 *
 * UN SEUL require dans tout le projet : celui de l'autoloader.
 */

declare(strict_types=1);

require __DIR__ . '/autoload.php';

use App\Incantation;
use App\Sort;

$sort = new Sort('Boule de feu', 30);
echo (new Incantation())->lancer($sort) . PHP_EOL;

// ─────────────────── Exercice 5.4 : une classe qui n'existe pas ───────────────────

echo PHP_EOL . '── Et si la classe n\'existe pas ? ──' . PHP_EOL;

try {
    $truc = new \ClasseQuiNexistePas();
} catch (Error $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
    echo '   (message clair, grâce au file_exists() de l\'autoloader)' . PHP_EOL;
}

echo PHP_EOL . <<<'TXT'
💡 CE QUE VOUS AVEZ APPRIS

   spl_autoload_register()   enregistre une fonction que PHP appelle
                             quand il rencontre une classe inconnue

   str_replace('\\', '/')    transforme le namespace en chemin :
                             App\Sort  →  App/Sort  →  App/Sort.php

   __DIR__                   le dossier de CE fichier. Toujours utiliser
                             un chemin absolu : sinon tout dépend d'où
                             le script est lancé.

   file_exists()             on vérifie avant d'inclure

   👉 RÉPONSE 5.1 : avec 50 classes, il faudrait 50 lignes de require.
      Et il faudrait penser à les ajouter dans le bon ordre à chaque
      nouvelle classe. L'autoloader règle ça une fois pour toutes.

TXT;
