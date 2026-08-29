# Chapitre 3 — Les énumérations (PHP 8.1)

> 📖 Cours : [3. Les énumérations](../README.md#3-les-énumérations-php-81)

Un **enum** sert à représenter une liste de valeurs **fixée d'avance** : les jours de la semaine, les statuts d'une commande, les couleurs d'un jeu de cartes.

---

## 🚦 Exercice 3.1 — Lisez et exécutez ⭐

Recopiez, lancez, observez :

```php
<?php

enum Feu
{
    case Rouge;
    case Orange;
    case Vert;
}

$feu = Feu::Rouge;

echo $feu->name . PHP_EOL;                    // Rouge

var_dump($feu === Feu::Rouge);                // true
var_dump($feu === Feu::Vert);                 // false
```

**Question** : à quoi ressemble la syntaxe `Feu::Rouge` ? (Indice : vous l'avez déjà vue pour les **constantes de classe**.)

---

## 🎨 Exercice 3.2 — Une méthode dans l'enum ⭐

Un enum peut contenir des méthodes, comme une classe.

**À faire :** ajoutez cette méthode **à l'intérieur** de l'enum, après les `case` :

```php
    public function emoji(): string
    {
        return match ($this) {
            Feu::Rouge  => '🔴',
            Feu::Orange => '🟠',
            Feu::Vert   => '🟢',
        };
    }
```

Testez :

```php
echo Feu::Vert->emoji() . PHP_EOL;
```

**Résultat attendu :**

```text
🟢
```

💡 `match` ressemble à un `switch`, mais il **renvoie une valeur** et n'a pas besoin de `break`.

---

## ⏱️ Exercice 3.3 — Une deuxième méthode ⭐

**À faire :** sur le même modèle, ajoutez une méthode `duree(): int` qui renvoie le nombre de secondes : Rouge 25, Orange 5, Vert 30.

Testez :

```php
echo Feu::Orange->duree() . PHP_EOL;   // 5
```

---

## 🔁 Exercice 3.4 — Parcourir tous les cas ⭐

`Feu::cases()` renvoie un tableau contenant **tous** les cas de l'enum.

**À faire :** ajoutez ceci à la fin du fichier :

```php
foreach (Feu::cases() as $etat) {
    echo $etat->emoji() . ' ' . $etat->name . ' : ' . $etat->duree() . ' s' . PHP_EOL;
}
```

**Résultat attendu :**

```text
🔴 Rouge : 25 s
🟠 Orange : 5 s
🟢 Vert : 30 s
```

---

## 💾 Exercice 3.5 — L'enum « adossé » ⭐⭐

Pour **enregistrer un enum en base de données**, il faut lui associer une valeur (un texte ou un nombre).

**À faire :** créez un nouveau fichier avec ceci :

```php
<?php

enum Statut: string          // ← ": string" = enum adossé
{
    case Brouillon = 'brouillon';
    case Publie = 'publie';
    case Archive = 'archive';
}

// De l'enum vers le texte (pour un INSERT en base)
echo Statut::Publie->value . PHP_EOL;        // publie

// Du texte vers l'enum (après un SELECT en base)
$statut = Statut::from('brouillon');
echo $statut->name . PHP_EOL;                // Brouillon
```

**À faire ensuite :**

1. Ajoutez une méthode `libelle(): string` avec un `match` : Brouillon → `📝 En cours de rédaction`, Publie → `✅ En ligne`, Archive → `📦 Archivé`.
2. Affichez les trois libellés avec une boucle sur `Statut::cases()`.

---

## 💥 Exercice 3.6 — Quand la valeur n'existe pas ⭐⭐

**À faire :** testez ces deux lignes, une à la fois :

```php
$a = Statut::from('publiée');       // avec un accent : ça n'existe pas
```

```php
$b = Statut::tryFrom('publiée');
var_dump($b);
```

**Questions :**

1. Laquelle des deux **arrête** le programme ?
2. Que renvoie l'autre ?
3. Laquelle utiliseriez-vous pour un texte tapé par un utilisateur dans un formulaire ?

💡 **À retenir** : `from()` plante si la valeur est inconnue, `tryFrom()` renvoie `null`. Pour une donnée venue de l'extérieur, on utilise `tryFrom()` et on affiche un message poli.

---

## ✊✋✌️ Exercice 3.7 — Pierre-Feuille-Ciseaux ⭐⭐

Vous avez tout ce qu'il faut.

**À faire :**

1. Créez un enum `Coup: string` avec `Pierre`, `Feuille`, `Ciseaux`.
2. Ajoutez `emoji(): string` (✊ ✋ ✌️).
3. Ajoutez `bat(Coup $autre): bool` — la pierre bat les ciseaux, les ciseaux battent la feuille, la feuille bat la pierre.

💡 **Coup de pouce** pour `bat()` :

```php
    public function bat(Coup $autre): bool
    {
        return match ($this) {
            Coup::Pierre  => $autre === Coup::Ciseaux,
            Coup::Feuille => /* à vous */,
            Coup::Ciseaux => /* à vous */,
        };
    }
```

4. Testez :

```php
var_dump(Coup::Pierre->bat(Coup::Ciseaux));   // true
var_dump(Coup::Pierre->bat(Coup::Feuille));   // false
```

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus 1 — La partie complète en 5 manches</b></summary>

Ajoutez une méthode statique `aleatoire(): self` (indice : `self::cases()` + `array_rand()`), puis une classe `Partie` qui fait s'affronter le joueur et l'ordinateur en 5 manches, avec le score final.

</details>

<details>
<summary><b>Bonus 2 — Les autres exercices</b></summary>

- **Le menu de la cantine** : enum sur `int`, lecture d'un argument en ligne de commande
- **Avant / après** : pourquoi un enum vaut mieux que des chaînes de caractères
- **Le feu complet** : `suivant()`, simulation de deux cycles

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
