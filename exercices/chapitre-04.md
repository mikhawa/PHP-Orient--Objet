# Chapitre 4 — Les espaces de noms

> 📖 Cours : [4. Les espaces de noms](../README.md#4-les-espaces-de-noms)

Un **namespace**, c'est comme un dossier pour vos classes. Il sert à éviter que deux classes portant le même nom ne se gênent.

---

## 💥 Exercice 4.1 — Le problème ⭐

Recopiez et lancez :

```php
<?php

class Potion
{
    public function boire(): string { return '✨ +40 mana !'; }
}

class Potion
{
    public function boire(): string { return '💥 BOUM.'; }
}
```

**Question** : que dit PHP ? (Lisez le message en entier.)

💡 Deux classes ne peuvent pas porter le même nom. Or, dans un vrai projet, vous utiliserez des bibliothèques écrites par d'autres — et il y aura forcément un jour deux classes `User`, `Logger` ou `Client`.

---

## 📁 Exercice 4.2 — La solution ⭐

Créez **deux fichiers séparés**.

`PotionMage.php` :

```php
<?php

namespace Mages;          // ← toute première instruction du fichier

class Potion
{
    public function boire(): string { return '✨ +40 mana !'; }
}
```

`PotionAlchimiste.php` :

```php
<?php

namespace Alchimistes;

class Potion
{
    public function boire(): string { return '💥 BOUM.'; }
}
```

`boutique.php` :

```php
<?php

require 'PotionMage.php';
require 'PotionAlchimiste.php';

$a = new \Mages\Potion();
$b = new \Alchimistes\Potion();

echo $a->boire() . PHP_EOL;
echo $b->boire() . PHP_EOL;
```

**Résultat attendu :**

```text
✨ +40 mana !
💥 BOUM.
```

💡 Les deux classes s'appellent toujours `Potion`, mais leur **nom complet** diffère : `Mages\Potion` et `Alchimistes\Potion`. Plus de conflit.

---

## ✂️ Exercice 4.3 — Raccourcir avec `use` ⭐

Écrire `\Mages\Potion` à chaque fois, c'est long.

**À faire :** en haut de `boutique.php` (après `<?php`, avant le reste), ajoutez :

```php
use Mages\Potion;
```

Vous pouvez maintenant écrire simplement :

```php
$a = new Potion();
```

**Question** : essayez d'ajouter aussi `use Alchimistes\Potion;`. Que se passe-t-il ? Pourquoi ?

---

## 🏷️ Exercice 4.4 — L'alias avec `as` ⭐

Pour importer les deux malgré le nom identique, on leur donne un surnom.

**À faire :** remplacez vos `use` par :

```php
use Mages\Potion as PotionDeMana;
use Alchimistes\Potion as PotionInstable;
```

Puis :

```php
$a = new PotionDeMana();
$b = new PotionInstable();
```

**Résultat attendu** : le même qu'à l'exercice 4.2.

---

## 🪤 Exercice 4.5 — Le piège classique ⭐⭐

Celui-ci fera perdre une heure à quelqu'un un jour. Autant que ce soit maintenant, en 5 minutes.

**À faire :** créez ce fichier et lancez-le :

```php
<?php

namespace App;

class Calculatrice
{
    public function diviser(int $a, int $b): float
    {
        if ($b === 0) {
            throw new Exception('Division par zéro !');
        }

        return $a / $b;
    }
}

$calc = new Calculatrice();
echo $calc->diviser(10, 0);
```

**Questions :**

1. Quel est le message d'erreur ? Quelle classe PHP cherche-t-il **exactement** ?
2. Pourquoi ne trouve-t-il pas la classe `Exception`, qui existe pourtant dans PHP ?

**À faire ensuite :** corrigez en ajoutant un antislash : `throw new \Exception(...)`.

💡 **À retenir** : dans un fichier avec un `namespace`, les classes natives de PHP (`Exception`, `PDO`, `DateTime`…) doivent être précédées d'un `\`. Sans lui, PHP les cherche dans votre namespace.

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus 1 — Les deux guildes</b> (namespace ↔ dossier, alias, constantes de namespace)</summary>

Version complète de l'exercice 4.2, avec des classes qui portent vraiment un état.

**1. Deux fichiers, deux namespaces qui suivent l'arborescence des dossiers :**

`guildes/Mages/Potion.php`

```php
<?php
namespace Guildes\Mages;   // guildes/Mages  →  Guildes\Mages

class Potion
{
    public function __construct(
        public readonly string $nom,
        public readonly int $mana,
    ) {}

    public function boire(): string
    {
        return "✨ +{$this->mana} mana ! Vous scintillez légèrement.";
    }
}

const DEVISE = 'La connaissance avant la puissance.';  // une constante DANS un namespace
```

`guildes/Alchimistes/Potion.php` : même structure, namespace `Guildes\Alchimistes`, une méthode `boire()` qui renvoie plutôt `"💥 BOUM. (dégâts : {$this->degats})"`, et sa propre `DEVISE`.

**2. Un fichier `boutique.php`** qui inclut les deux (`require_once` — l'autoload, c'est le chapitre 5) et les utilise de **deux façons** :

- avec le **nom pleinement qualifié** : `new \Guildes\Mages\Potion('Élixir', 40)` ;
- avec des **alias** (dans un second fichier, car `use` doit être en haut) :

  ```php
  use Guildes\Mages\Potion as PotionDeMana;
  use Guildes\Alchimistes\Potion as PotionInstable;
  ```

**3. Les constantes de namespace :** affichez `\Guildes\Mages\DEVISE` et `\Guildes\Alchimistes\DEVISE`.

> ⚠️ Piège à noter en commentaire : une constante de namespace s'écrit `\Guildes\Mages\DEVISE`, **pas** `Guildes\Mages::DEVISE` (ça, c'est une constante de *classe*). Et `use` en haut d'un fichier = importer un namespace ; `use` dans une classe = utiliser un *trait*. Rien à voir.

</details>

<details>
<summary><b>Bonus 2 — Le piège de l'Exception (version complète)</b></summary>

Reprenez le code de l'exercice 4.5 et répondez **par écrit** aux questions, puis vérifiez chaque réponse.

```php
<?php
namespace App\Service;

class Calculatrice
{
    public function diviser(float $a, float $b): float
    {
        if ($b == 0.0) {
            throw new Exception('Division par zéro !');
        }
        return $a / $b;
    }
}

try {
    echo (new Calculatrice())->diviser(10, 0);
} catch (Exception $e) {
    echo 'Attrapée : ' . $e->getMessage();
}
```

1. **Quel nom exact** PHP cherche-t-il quand il rencontre `new Exception(...)` dans ce fichier ? (Réponse : `App\Service\Exception` — tout nom de classe non préfixé est résolu **relativement au namespace courant**.)
2. **Pourquoi** `strlen()` ou `count()` fonctionnent sans backslash dans un namespace, mais pas `Exception` ? (Réponse : le repli automatique vers le namespace global ne vaut que pour les **fonctions et constantes** natives, jamais pour les **classes**.)
3. **Corrigez de deux façons** :
   - le préfixe : `throw new \Exception(...)` ;
   - l'importation : `use Exception;` en haut du fichier, puis `throw new Exception(...)`.
4. **Le bug caché dans le `catch`** : même si le `throw` fonctionnait, `catch (Exception $e)` signifie `catch (\App\Service\Exception $e)` → il n'attraperait **jamais** une vraie `\Exception`. Corrigez-le aussi.

**Règle à retenir** : dans un fichier avec `namespace`, préfixez toujours les classes natives (`\Exception`, `\PDO`, `\DateTimeImmutable`) ou importez-les en haut avec `use`.

</details>

<details>
<summary><b>Bonus 3 — Le cartographe de projet</b> (organiser un projet en namespaces, préparation du chapitre 5)</summary>

Exercice **sur papier** (aucun code à exécuter). On vous donne une liste de classes d'un petit jeu :

`Hero`, `Monstre`, `Objet`, `HeroManager`, `MonstreManager`, `Combat`, `GenerateurDeDonjon`, `HeroMortException`, `DonjonVideException`, plus un point d'entrée `public/index.php`.

**Votre mission :** proposez une arborescence de dossiers et les namespaces correspondants, en respectant ces règles (celles que l'autoloader du chapitre 5 saura résoudre) :

- **un segment de namespace = un dossier**, à la casse exacte (`App\Manager\HeroManager` → `App/Manager/HeroManager.php`) ;
- **un fichier = une classe**, du nom exact de la classe ;
- on regroupe les classes par **rôle technique** : `Model/`, `Manager/`, `Service/`, `Exception/` ;
- **aucune classe dans le namespace global** (pour ne jamais entrer en collision avec une lib tierce ou une future classe native) ;
- `public/index.php` **n'a pas de namespace** : ce n'est pas une classe, c'est un point d'entrée.

Écrivez ensuite, en une ou deux phrases, **pourquoi** ce découpage par rôle (et non « tout ce qui touche au héros dans un dossier `Hero/` »). Puis montrez la transformation `App\Manager\HeroManager` → chemin de fichier que fera l'autoloader :

```php
str_replace('\\', '/', 'App\Manager\HeroManager') . '.php'
// → 'App/Manager/HeroManager.php'
```

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
