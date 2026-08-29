<?php
/**
 * Corrigé 5.5 — L'autoloader final
 */

spl_autoload_register(function (string $nomDeClasse) {
    // LA ligne clé : App\Sort  →  App/Sort
    $fichier = __DIR__ . '/' . str_replace('\\', '/', $nomDeClasse) . '.php';

    // On vérifie AVANT d'inclure (exercice 5.4) : sinon, un fichier
    // manquant fait tout planter avec un message incompréhensible.
    if (file_exists($fichier)) {
        require $fichier;
    }
});
