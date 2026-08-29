# Corrigé 5.C — Sous le capot de Composer

## Les fichiers

**`composer.json`**

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

Cette ligne se lit : « toute classe dont le nom commence par `App\` se trouve dans le dossier `src/`, à un chemin obtenu en remplaçant les `\` par des `/` ».

**L'arborescence**

```text
projet/
├── composer.json
├── vendor/            ← généré, à ne JAMAIS committer (.gitignore)
│   └── autoload.php
├── src/
│   ├── Model/
│   │   ├── Sort.php        → App\Model\Sort
│   │   └── Grimoire.php    → App\Model\Grimoire
│   └── Service/
│       └── Incantation.php → App\Service\Incantation
└── public/
    └── index.php
```

Notez que le namespace **ne change pas** : `App\Model\Sort` reste `App\Model\Sort`. Seul le dossier de base change (`App/` devient `src/`), et c'est précisément le rôle du mapping PSR-4 : découpler le préfixe de namespace du nom réel du dossier.

**`public/index.php`**

```php
require __DIR__ . '/../vendor/autoload.php';   // au lieu de votre autoload.php

use App\Model\Grimoire;
// … le reste du code est identique
```

## Ce que révèle `vendor/composer/autoload_psr4.php`

Après `composer dump-autoload`, ouvrez ce fichier :

```php
<?php
$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'App\\' => array($baseDir . '/src'),
);
```

C'est **votre mapping**, converti en tableau PHP. Composer génère aussi `autoload_classmap.php` (une table classe → fichier, pour les cas non PSR-4) et `ClassLoader.php`, qui contient la vraie logique — et qui fait, en substance, ce que vous avez écrit à la main :

```php
// version très simplifiée de ce que fait ClassLoader::findFile()
foreach ($prefixesPsr4 as $prefix => $dirs) {
    if (str_starts_with($class, $prefix)) {
        $reste = substr($class, strlen($prefix));
        foreach ($dirs as $dir) {
            $file = $dir . '/' . str_replace('\\', '/', $reste) . '.php';
            if (file_exists($file)) {
                return $file;
            }
        }
    }
}
```

## Ce que Composer fait en plus

Votre autoloader maison est correct, mais celui de Composer ajoute :

| Fonctionnalité | À quoi ça sert |
|----------------|----------------|
| **Classmap optimisée** (`--optimize`) | Un tableau `classe => fichier` pré-calculé : plus aucun `file_exists()` à l'exécution. Indispensable en production. |
| **Plusieurs préfixes** | `App\` → `src/`, `Tests\` → `tests/`, plus tous les paquets de `vendor/`, chacun avec son mapping. |
| **`files`** | Charger des fichiers de fonctions (helpers) à chaque requête, pas seulement des classes. |
| **APCu** | Mise en cache mémoire des chemins résolus. |
| **Fallback** | Plusieurs dossiers possibles pour un même préfixe. |

## La morale

Vous savez maintenant lire `vendor/autoload.php` sans avoir peur. Ce n'est pas de la magie : c'est un `spl_autoload_register()` avec un tableau de correspondances bien organisé — exactement ce que vous avez écrit en dix lignes à l'exercice 5.A. 🔧

> 💡 **Le jour où l'autoload « ne marche pas »**, les trois causes sont presque toujours : la casse du nom de fichier (`sort.php` au lieu de `Sort.php`), un namespace qui ne correspond pas au dossier, ou un `composer dump-autoload` oublié après avoir ajouté une classe.
