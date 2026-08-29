# Chapitre 7 — Le `match` et les nouveautés PHP 8

> 📖 Cours : [7. Le match et les autres nouveautés PHP 8 utiles](../README.md#7-le-match-et-les-autres-nouveautés-php-8-utiles)

Quelques outils modernes qui rendent le code plus court et plus sûr.

---

## 🔀 Exercice 7.1 — `switch` contre `match` ⭐

Voici du code que vous savez déjà écrire :

```php
<?php

function emojiMeteo(string $temps): string
{
    switch ($temps) {
        case 'soleil':
            return '☀️';
        case 'pluie':
            return '🌧️';
        case 'neige':
            return '❄️';
        default:
            return '🤷';
    }
}

echo emojiMeteo('pluie') . PHP_EOL;
```

**À faire :** réécrivez cette fonction avec `match` :

```php
function emojiMeteo(string $temps): string
{
    return match ($temps) {
        'soleil' => '☀️',
        'pluie'  => '🌧️',
        'neige'  => '❄️',
        default  => '🤷',
    };
}
```

**Questions :**

1. Combien de lignes en moins ?
2. Voyez-vous des `break` ? Des `return` répétés ?

💡 `match` **renvoie** une valeur : on écrit `return match(...)` une seule fois.

---

## 🎯 Exercice 7.2 — Regrouper des cas ⭐

**À faire :** ajoutez une fonction `typeDeJour(string $jour): string` avec un `match`, sachant que samedi et dimanche donnent le même résultat :

```php
    return match ($jour) {
        'samedi', 'dimanche' => 'week-end 🎉',
        default              => 'boulot 😅',
    };
```

Testez avec plusieurs jours.

💡 Plusieurs valeurs séparées par des virgules partagent le même résultat. En `switch`, il fallait empiler les `case` sans `break`.

---

## 💥 Exercice 7.3 — Le filet de sécurité ⭐

**À faire :** supprimez la ligne `default` de `emojiMeteo()`, puis appelez `emojiMeteo('brouillard')`.

**Questions :**

1. Comment s'appelle l'erreur ?
2. Avec un `switch` sans `default`, que se serait-il passé ?

💡 **À retenir** : un `match` sans `default` **exige** que tous les cas soient traités. Le jour où vous ajoutez un cas et oubliez de le gérer, PHP vous arrête tout de suite. Un `switch` serait resté silencieux — et le bug serait parti en production.

---

## 📊 Exercice 7.4 — `match(true)` pour des intervalles ⭐⭐

`match` compare des valeurs exactes. Pour tester des **conditions**, on écrit `match(true)`.

**À faire :** recopiez et complétez :

```php
function mention(int $note): string
{
    return match (true) {
        $note >= 16 => 'Excellent 🏆',
        $note >= 14 => 'Bien 😀',
        $note >= 10 => 'Passable 🙂',
        default     => 'À revoir 😬',
    };
}

foreach ([18, 15, 11, 4] as $note) {
    echo $note . '/20 : ' . mention($note) . PHP_EOL;
}
```

**Résultat attendu :**

```text
18/20 : Excellent 🏆
15/20 : Bien 😀
11/20 : Passable 🙂
4/20 : À revoir 😬
```

⚠️ **L'ordre compte** : PHP prend la **première** condition vraie. Inversez les deux premières lignes et regardez ce qui se casse.

---

## 🛡️ Exercice 7.5 — L'opérateur `?->` ⭐⭐

**À faire :** recopiez et lancez :

```php
<?php

class Ville
{
    public function __construct(public string $nom) {}
}

class Utilisateur
{
    public function __construct(
        public string $pseudo,
        public ?Ville $ville = null,     // le ? veut dire « ou null »
    ) {}
}

$aline = new Utilisateur('Aline', new Ville('Charleroi'));
$bob = new Utilisateur('Bob');           // pas de ville

echo $aline->ville->nom . PHP_EOL;       // Charleroi
echo $bob->ville->nom . PHP_EOL;         // 💥 problème !
```

**Question** : quel message apparaît pour Bob ?

**À faire ensuite :** remplacez les deux dernières lignes par :

```php
echo ($aline->ville?->nom ?? 'ville inconnue') . PHP_EOL;
echo ($bob->ville?->nom ?? 'ville inconnue') . PHP_EOL;
```

**Résultat attendu :**

```text
Charleroi
ville inconnue
```

💡 **À retenir** : `?->` veut dire « si c'est `null`, arrête-toi là et renvoie `null` ». Et `??` veut dire « si c'est `null`, prends cette valeur à la place ».

---

## 🏷️ Exercice 7.6 — Les arguments nommés ⭐

**À faire :** recopiez cette classe :

```php
class Pizza
{
    public function __construct(
        public string $pate = 'classique',
        public string $sauce = 'tomate',
        public bool $extraFromage = false,
        public bool $ananas = false,
    ) {}
}
```

Commandez une pizza avec **uniquement** de l'ananas, sans rien préciser d'autre :

```php
$p = new Pizza(ananas: true);
```

**Question** : sans les arguments nommés, comment auriez-vous dû écrire cette ligne ?

💡 **Réponse** : `new Pizza('classique', 'tomate', false, true)` — il aurait fallu répéter les trois valeurs par défaut, et les connaître par cœur.

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus — Les exercices détaillés</b></summary>

- **Le Choixpeau magique** : `match(true)`, `switch` vs `match`, `UnhandledMatchError`
- **Le GPS prudent** : `?->` en profondeur, Warning ≠ Error
- **L'usine à gâteaux** : arguments nommés, `new` dans un initialiseur
- **La chaîne de fabrication** (difficile) : callables de première classe

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
