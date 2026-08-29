<?php
spl_autoload_register(function (string $class): void {
    $chemin = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($chemin)) {
        require_once $chemin;
    }
});
