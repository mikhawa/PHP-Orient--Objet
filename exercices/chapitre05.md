# Chapitre 5 — Auto-chargement des classes

> 📖 Cours : [5. Auto-chargement des classes](../README.md#5-auto-chargement-des-classes)

---

## 📜 Exercice 5.A — Le grimoire auto-magique ⭐⭐

Fini les `require_once` à rallonge : votre grimoire va invoquer ses classes tout seul.

1. Créez cette arborescence (reprenez le plan dessiné à l'exercice 4.C !) :

```text
grimoire/
├── autoload.php
├── App/
│   ├── Model/
│   │   ├── Sort.php           → App\Model\Sort
│   │   └── Grimoire.php       → App\Model\Grimoire
│   └── Service/
│       └── Incantation.php    → App\Service\Incantation
└── public/
    └── index.php
```

2. Contenu des classes (simple, l'exercice porte sur l'autoload) :
    - `Sort` : promotion de propriétés `(public readonly string $nom, public readonly int $mana)` ;
    - `Grimoire` : `ajouter(Sort $sort)`, `nombreDeSorts(): int` ;
    - `Incantation` : `lancer(Sort $sort): string` → `"🪄 Abracadabra ! {nom} lancé pour {mana} mana."`
3. Écrivez `autoload.php` avec `spl_autoload_register()` : transformation du namespace en chemin, vérification `file_exists()`, `require_once`.
4. Dans `public/index.php` : **un seul** `require_once __DIR__ . '/../autoload.php';`, puis utilisez les trois classes. Aucun autre `require` ne doit apparaître !
5. Vérifiez que tout fonctionne : `php public/index.php`.

⚠️ Piège classique : le chemin de base de l'autoloader doit être **absolu** (`__DIR__`), sinon tout dépend du dossier depuis lequel on lance le script. Testez en lançant depuis `grimoire/` ET depuis `grimoire/public/` — ça doit marcher dans les deux cas.

---

## 🕵️ Exercice 5.B — L'espion de l'autoloader ⭐

L'autoload est **paresseux** : il ne charge une classe qu'au moment précis où on en a besoin. Prouvez-le !

1. Dans votre autoloader, ajoutez temporairement un mouchard :

```php
echo "🔎 Chargement de $class..." . PHP_EOL;
```

2. Dans `index.php`, écrivez ce scénario et **prédisez l'ordre des messages avant d'exécuter** :

```php
require_once __DIR__ . '/../autoload.php';

echo "-- début du script --" . PHP_EOL;

$grimoire = new App\Model\Grimoire();        // charge quoi ?
echo "-- grimoire créé --" . PHP_EOL;

if (false) {
    $jamais = new App\Service\Incantation(); // charge quoi ?!
}
echo "-- fin du script --" . PHP_EOL;
```

3. `Incantation` a-t-elle été chargée ? Pourquoi est-ce une excellente nouvelle pour les performances d'un gros projet (des centaines de classes) ?
4. Retirez le mouchard (un autoloader qui `echo`, c'est bien pour apprendre, pas pour la production 😄).

---

## 📦 Exercice 5.C — Sous le capot de Composer ⭐⭐⭐ (bonus)

Pour celles et ceux qui ont Composer installé :

1. Dans un nouveau dossier, créez un `composer.json` minimal :

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

2. Lancez `composer dump-autoload`, déplacez vos classes du grimoire dans `src/Model/` et `src/Service/` (namespace inchangé : `App\Model`, `App\Service`).
3. Remplacez votre `autoload.php` maison par `require 'vendor/autoload.php';`.
4. Ouvrez `vendor/composer/autoload_psr4.php` et retrouvez votre mapping. Moralité : Composer fait exactement ce que vous avez écrit à la main en 5.A — vous savez désormais lire sous le capot. 🔧

---

[⬅️ Retour à l'index des exercices](./README.md)
