# Chapitre 4 — Les espaces de noms

> 📖 Cours : [4. Les espaces de noms](../README.md#4-les-espaces-de-noms)

---

## 🧙 Exercice 4.A — Les deux guildes ⭐⭐

Deux guildes rivales vendent des potions… et ont **chacune** une classe `Potion`. Sans namespaces, collision fatale garantie !

1. Créez cette arborescence (les `require_once` restent manuels : l'autoload, c'est le chapitre suivant) :

```text
guildes/
├── Mages/
│   └── Potion.php        → namespace Guildes\Mages;
├── Alchimistes/
│   └── Potion.php        → namespace Guildes\Alchimistes;
└── boutique.php
```

2. `Guildes\Mages\Potion` : constructeur `(string $nom, int $mana)`, méthode `boire(): string` → `"✨ +40 mana ! Vous scintillez légèrement."`
3. `Guildes\Alchimistes\Potion` : constructeur `(string $nom, int $degatsExplosion)`, méthode `boire(): string` → `"💥 BOUM. Les alchimistes déclinent toute responsabilité."`
4. Dans `boutique.php`, instanciez **les deux** potions dans le même fichier :
    - une fois avec les **noms pleinement qualifiés** (`new \Guildes\Mages\Potion(...)`) ;
    - une fois avec des **alias** `use ... as PotionDeMana;` / `use ... as PotionInstable;`.
5. Affichez le résultat de `boire()` pour chacune.

**Bonus** ⭐⭐ : ajoutez une constante `const DEVISE = '...';` dans chaque namespace (hors classe) et affichez les deux devises — attention à la syntaxe d'accès aux constantes de namespace !

---

## 💣 Exercice 4.B — Le piège de l'Exception ⭐⭐

Ce fichier plante avec une erreur mystérieuse. À vous de jouer, sans le modifier d'abord :

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

$calc = new Calculatrice();

try {
    echo $calc->diviser(10, 0);
} catch (Exception $e) {
    echo 'Attrapée : ' . $e->getMessage();
}
```

1. Exécutez et lisez **attentivement** le message d'erreur. Quelle classe PHP cherche-t-il exactement ? (Indice : le namespace s'invite dans le nom…)
2. Expliquez en une phrase pourquoi `Exception` ne désigne pas ici la classe native de PHP.
3. Corrigez le code de **deux manières différentes** (préfixe `\` ; importation `use`). Les deux `Exception` du fichier sont concernées !
4. Question piège : pourquoi le `catch` non corrigé n'attraperait-il jamais rien, même si le `throw` fonctionnait ?

---

## 🗺️ Exercice 4.C — Cartographe de projet ⭐

Sur papier (ou en commentaire), proposez une organisation en namespaces pour ce futur projet de jeu :

- des modèles : `Hero`, `Monstre`, `Objet` ;
- des managers PDO : `HeroManager`, `MonstreManager` ;
- des services : `Combat`, `GenerateurDeDonjon` ;
- des exceptions : `HeroMortException`, `DonjonVideException`.

Contraintes : un préfixe racine `App`, une correspondance namespace ↔ dossier stricte (préparation PSR-4), et aucune classe dans le namespace global. Justifiez vos choix en deux phrases. Vous réutiliserez ce plan au chapitre suivant !

---

[⬅️ Retour à l'index des exercices](./README.md)
