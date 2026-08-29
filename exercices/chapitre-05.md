# Chapitre 5 — Auto-chargement des classes

> 📖 Cours : [5. Auto-chargement des classes](../README.md#5-auto-chargement-des-classes)

Objectif : ne plus jamais écrire de `require` pour charger une classe.

---

## 😩 Exercice 5.1 — Le problème ⭐

Créez trois fichiers dans un dossier `grimoire/` :

`Sort.php` :

```php
<?php

class Sort
{
    public function __construct(
        public string $nom,
        public int $mana,
    ) {}
}
```

`Incantation.php` :

```php
<?php

class Incantation
{
    public function lancer(Sort $sort): string
    {
        return '🪄 ' . $sort->nom . ' lancé pour ' . $sort->mana . ' mana.';
    }
}
```

`index.php` :

```php
<?php

require 'Sort.php';
require 'Incantation.php';

$sort = new Sort('Boule de feu', 30);
echo (new Incantation())->lancer($sort) . PHP_EOL;
```

**Résultat attendu :**

```text
🪄 Boule de feu lancé pour 30 mana.
```

**Question** : avec 50 classes, combien de lignes `require` faudrait-il en haut de `index.php` ?

---

## ✨ Exercice 5.2 — L'auto-chargement ⭐

**À faire :** remplacez les **deux** lignes `require` de `index.php` par ce bloc :

```php
spl_autoload_register(function (string $nomDeClasse) {
    echo "🔎 PHP a besoin de la classe $nomDeClasse, je la charge..." . PHP_EOL;
    require __DIR__ . '/' . $nomDeClasse . '.php';
});
```

Relancez.

**Résultat attendu :**

```text
🔎 PHP a besoin de la classe Sort, je la charge...
🔎 PHP a besoin de la classe Incantation, je la charge...
🪄 Boule de feu lancé pour 30 mana.
```

💡 **Ce qui se passe** : quand PHP rencontre une classe qu'il ne connaît pas, il appelle votre fonction en lui passant le nom. À vous de lui indiquer le fichier.

---

## 😴 Exercice 5.3 — Le chargement paresseux ⭐

**À faire :** dans `index.php`, commentez la ligne qui utilise `Incantation` :

```php
$sort = new Sort('Boule de feu', 30);
// echo (new Incantation())->lancer($sort) . PHP_EOL;
```

Relancez.

**Question** : le message `🔎 ... Incantation ...` apparaît-il encore ? Pourquoi ?

💡 **À retenir** : une classe n'est chargée **qu'au moment exact où on l'utilise**. Sur un projet de 5 000 classes, une page n'en charge que celles dont elle a besoin. C'est pour ça que l'auto-chargement n'est pas seulement pratique : il est rapide.

---

## 🛡️ Exercice 5.4 — Rendre l'autoloader prudent ⭐⭐

Actuellement, si le fichier n'existe pas, tout plante.

**À faire :**

1. Testez : `$truc = new ClasseQuiNexistePas();` → erreur brutale.
2. Corrigez en vérifiant l'existence du fichier :

```php
spl_autoload_register(function (string $nomDeClasse) {
    $fichier = __DIR__ . '/' . $nomDeClasse . '.php';

    if (file_exists($fichier)) {
        require $fichier;
    }
});
```

3. Relancez le test. Le message d'erreur est-il plus clair ?

💡 On retire aussi le `echo` de débogage : un autoloader qui affiche du texte casserait n'importe quel site web.

---

## 📂 Exercice 5.5 — Namespaces et dossiers ⭐⭐

On combine avec le chapitre 4. Réorganisez :

```text
grimoire/
├── index.php
├── autoload.php
└── App/
    ├── Sort.php           →  namespace App;
    └── Incantation.php    →  namespace App;
```

**À faire :**

1. Ajoutez `namespace App;` en haut de `Sort.php` et de `Incantation.php`, et déplacez-les dans `App/`.
2. Mettez l'autoloader dans son propre fichier `autoload.php`, avec cette ligne clé :

```php
    // App\Sort  →  App/Sort
    $fichier = __DIR__ . '/' . str_replace('\\', '/', $nomDeClasse) . '.php';
```

3. Dans `index.php` : **un seul** `require 'autoload.php';`, puis `use App\Sort;` et `use App\Incantation;`.

**Résultat attendu** : le même qu'au départ, mais avec un seul `require` dans tout le projet. 🎉

💡 **La ligne à comprendre** : `str_replace('\\', '/', ...)` transforme le namespace en chemin de dossier. C'est **toute** l'astuce de l'auto-chargement moderne — et c'est exactement ce que fait Composer.

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus — Les exercices détaillés</b></summary>

- **Le grimoire complet**, avec `App/Model/` et `App/Service/`
- **L'espion de l'autoloader**, qui prouve le chargement paresseux
- **Sous le capot de Composer** : à quoi ressemble `vendor/autoload.php`

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
