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
<summary><b>Bonus — Les corrigés détaillés</b></summary>

- **Les deux guildes**, version complète avec constantes de namespace — [`solutions/chapitre-04/guildes/`](../solutions/chapitre-04/guildes/)
- **Le piège de l'Exception**, avec les deux façons de le corriger — [`04b-piege-exception.php`](../solutions/chapitre-04/04b-piege-exception.php)
- **Organiser un projet** en namespaces, en préparation du chapitre 5 — [`04c-cartographe-corrige.md`](../solutions/chapitre-04/04c-cartographe-corrige.md)

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
