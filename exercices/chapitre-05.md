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
<summary><b>Bonus 1 — Le grimoire complet</b> (namespaces à plusieurs niveaux, un seul <code>require</code>)</summary>

On passe de deux classes à trois, réparties sur **deux sous-namespaces**.

**Arborescence :**

```text
grimoire/
├── autoload.php
├── public/
│   └── index.php                        (pas de namespace)
└── App/
    ├── Model/
    │   ├── Sort.php        → App\Model\Sort
    │   └── Grimoire.php    → App\Model\Grimoire
    └── Service/
        └── Incantation.php → App\Service\Incantation
```

**Les classes :**

- `App\Model\Sort` : `public function __construct(public readonly string $nom, public readonly int $mana) {}` ;
- `App\Model\Grimoire` : une propriété privée `array $sorts = []`, et les méthodes `ajouter(Sort $sort): void`, `nombreDeSorts(): int`, `getSorts(): array` ;
- `App\Service\Incantation` : doit manipuler un `Sort`, qui vit dans un **autre** namespace → il faut donc `use App\Model\Sort;` en haut du fichier. Méthode `lancer(Sort $sort): string` qui renvoie `"🪄 Abracadabra ! Boule de feu lancé pour 30 mana."`.

**`autoload.php` :** un `spl_autoload_register()` qui construit un chemin **absolu** (avec `__DIR__`, jamais relatif) :

```php
$chemin = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
if (file_exists($chemin)) {           // on teste AVANT d'inclure
    require_once $chemin;
}
```

**`public/index.php` :** un seul `require __DIR__ . '/../autoload.php';`, puis les trois `use`, puis on remplit un grimoire de 3 sorts et on les lance tous dans une boucle. Objectif : **zéro `require`** en dehors de l'autoloader.

</details>

<details>
<summary><b>Bonus 2 — L'espion de l'autoloader</b> (prouver le chargement paresseux)</summary>

But : constater de ses yeux qu'une classe n'est chargée **qu'au moment exact** où PHP en a besoin.

**1. Un autoloader mouchard** — comme celui du bonus 1, mais avec une trace :

```php
spl_autoload_register(function (string $class): void {
    $chemin = __DIR__ . '/grimoire/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($chemin)) {
        echo "🔎 Chargement de $class..." . PHP_EOL;   // le mouchard
        require_once $chemin;
    }
});
```

**2. Un scénario piégé :**

```php
echo "-- début --" . PHP_EOL;
$grimoire = new App\Model\Grimoire();
echo "-- grimoire créé --" . PHP_EOL;

if (false) {                                  // jamais exécuté
    $jamais = new App\Service\Incantation();
}
echo "-- fin --" . PHP_EOL;
```

**3. Les questions :**

- Combien de lignes `🔎` apparaissent ? Lesquelles ? (Réponse : une seule, pour `Grimoire`.)
- `App\Service\Incantation` a-t-elle été chargée ? Vérifiez avec `class_exists('App\Service\Incantation', autoload: false)`.
- Pourquoi `App\Model\Sort` n'apparaît pas non plus ?
- Mentionnez `Incantation` **après** le `echo "-- fin --"` : le `🔎` apparaît maintenant. À la demande, pas une milliseconde avant.

**Pourquoi c'est important** : un projet Symfony/Laravel a des milliers de classes dans `vendor/`. Sans autoload paresseux, chaque requête HTTP les inclurait toutes. Avec, une page qui utilise 20 classes en charge 20.

> 🧹 Terminez en retirant le `echo` du mouchard : un autoloader qui affiche quoi que ce soit casse tout site web (impossible d'envoyer un en-tête HTTP après le moindre caractère) et pollue toute sortie JSON/CSV.

</details>

<details>
<summary><b>Bonus 3 — Sous le capot de Composer</b> (PSR-4, <code>vendor/autoload.php</code>)</summary>

Exercice de lecture : comprendre que Composer ne fait « que » ce que vous avez écrit à la main.

**1. Créez un vrai mini-projet Composer :**

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

Cette ligne se lit : « toute classe commençant par `App\` se trouve dans `src/`, au chemin obtenu en remplaçant les `\` par des `/` ». Notez que le **namespace ne change pas** (`App\Model\Sort` reste `App\Model\Sort`) : seul le dossier de base passe de `App/` à `src/`.

Rangez-y vos classes du bonus 1 (`src/Model/Sort.php`, etc.), lancez `composer dump-autoload`, et dans `public/index.php` remplacez votre `require` par :

```php
require __DIR__ . '/../vendor/autoload.php';
```

Le reste du code ne bouge pas.

**2. Ouvrez `vendor/composer/autoload_psr4.php`.** Vous y trouverez **votre** mapping, converti en tableau PHP :

```php
return array(
    'App\\' => array($baseDir . '/src'),
);
```

**3. Ouvrez `vendor/composer/ClassLoader.php`** et retrouvez la fonction `findFile()`. En substance, elle fait :

```php
foreach ($prefixesPsr4 as $prefix => $dirs) {
    if (str_starts_with($class, $prefix)) {
        $reste = substr($class, strlen($prefix));
        foreach ($dirs as $dir) {
            $file = $dir . '/' . str_replace('\\', '/', $reste) . '.php';
            if (file_exists($file)) return $file;
        }
    }
}
```

… c'est-à-dire exactement votre autoloader du bonus 1, avec un tableau de préfixes en plus.

**4. Rédigez une courte note** : que Composer ajoute en plus (classmap optimisée `--optimize` qui supprime les `file_exists()`, plusieurs préfixes, chargement de fichiers `files`, cache APCu), et les **trois causes** habituelles d'un « autoload qui ne marche pas » : casse du nom de fichier, namespace ≠ dossier, `composer dump-autoload` oublié.

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
