<?php
/**
 * Corrigé 5.A — L'autoloader du grimoire.
 *
 * spl_autoload_register() enregistre une fonction que PHP appellera
 * AUTOMATIQUEMENT chaque fois qu'il rencontre une classe inconnue.
 * Elle reçoit le nom COMPLET de la classe, namespace inclus.
 */

spl_autoload_register(function (string $class): void {
    // ⚠️ __DIR__ = le dossier de CE fichier : le chemin est donc ABSOLU.
    // Avec un chemin relatif ('./App/...'), tout dépendrait du dossier
    // depuis lequel le script est lancé — et ça casserait une fois sur deux.
    $base = __DIR__;

    // App\Model\Sort  →  App/Model/Sort
    $chemin = $base . '/' . str_replace('\\', '/', $class) . '.php';

    // On vérifie AVANT d'inclure : un require sur un fichier absent est fatal,
    // et surtout d'autres autoloaders (Composer…) doivent pouvoir prendre le relais.
    if (file_exists($chemin)) {
        require_once $chemin;
    }
});
