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
<summary><b>Bonus 1 — La partie complète de Pierre-Feuille-Ciseaux</b> (méthode statique, <code>cases()</code>)</summary>

Reprenez votre enum `Coup: string` (`Pierre = 'pierre'`, etc.) et sa méthode `bat(Coup $autre): bool`.

**1. Deux méthodes en plus sur l'enum :**

- `emoji(): string` → `✊` / `✋` / `✌️` selon le cas ;
- `public static function aleatoire(): self` → renvoie un cas tiré au hasard. Indice : `self::cases()` renvoie le tableau de tous les cas, puis `$tous[array_rand($tous)]`.

**2. Une classe `Partie` :**

- `private int $scoreJoueur = 0;` et `private int $scoreOrdinateur = 0;` ;
- constructeur `public function __construct(private int $manches = 5) {}` ;
- `jouer(): void` — boucle sur `$manches` : tire un coup pour le joueur et un pour l'ordinateur (avec `Coup::aleatoire()`), détermine le vainqueur de la manche avec un `match(true)` (égalité / le joueur `bat()` / sinon l'ordinateur), met à jour les scores, et affiche chaque manche : `Manche 1 : Vous ✊ vs 🤖 ✌️ → vous gagnez !` ;
- termine par un score final et une conclusion (`match(true)` sur la comparaison des scores).

**3. La conversion depuis une chaîne** (comme le ferait une saisie ou une colonne SQL) :

```php
$coup = Coup::from('ciseaux');        // Coup::Ciseaux
Coup::from('cisaux');                 // ValueError — la faute de frappe est détectée
var_dump(Coup::tryFrom('cisaux'));    // null — la version tolérante
```

> 💪 **Lezard-Spock** : ajoutez les cas `Lezard` et `Spock` et complétez `bat()` (chaque coup en bat deux). Si votre modélisation est bonne, la classe `Partie` ne bouge **pas d'une ligne**.

</details>

<details>
<summary><b>Bonus 2 — Le feu de signalisation complet</b> (enum <b>pur</b>, <code>match</code> qui renvoie un cas)</summary>

Un enum **pur** (sans `: string` ni `: int`) suffit ici : les états d'un feu n'ont pas besoin d'être stockés en base.

```php
enum Feu
{
    case Rouge;
    case Orange;
    case Vert;
}
```

**Méthodes à ajouter** (toutes en `match($this)`) :

- `suivant(): self` — renvoie **l'état suivant** : Vert → Orange, Orange → Rouge, Rouge → Vert. (Oui, un `match` peut renvoyer un autre cas de l'enum.)
- `duree(): int` — Vert : 30 s, Orange : 5 s, Rouge : 25 s ;
- `emoji(): string` — 🟢 / 🟠 / 🔴 ;
- `libelle(): string` — `'Vert'` / `'Orange'` / `'Rouge'` (ou utilisez directement `$this->name`) ;
- `peutPasser(bool $estUnTaxiPresse = false): bool` — `true` au vert, `false` au rouge, et **`$estUnTaxiPresse`** à l'orange 🚕.

**Programme de test** — simulez **2 cycles complets** (6 changements d'état) en partant du vert, en affichant à chaque étape l'emoji, le libellé et la durée, et en cumulant la durée totale :

```text
🟢 Vert pendant 30 s
🟠 Orange pendant 5 s
🔴 Rouge pendant 25 s
🟢 Vert pendant 30 s
...
Durée totale : 180 s
```

Puis affichez un tableau « qui peut passer ? » pour un conducteur normal vs un taxi pressé, en bouclant sur `Feu::cases()`.

</details>

<details>
<summary><b>Bonus 3 — Le menu de la cantine</b> (enum adossé sur <code>int</code>, <code>tryFrom()</code>, argument CLI)</summary>

```php
enum Jour: int
{
    case Lundi = 1;
    case Mardi = 2;
    // ... jusqu'à
    case Dimanche = 7;
}
```

**Méthodes :**

- `menu(): string` — le plat du jour. Pour le week-end, **regroupez les cas** : `Jour::Samedi, Jour::Dimanche => 'La cantine est fermée'` ;
- `estOuvert(): bool` — `false` le week-end (`match` avec `default => true`) ;
- `public static function aujourdhui(): self` — renvoie le jour réel : `self::from((int) date('N'))` (`date('N')` donne 1 pour lundi … 7 pour dimanche).

**Le programme lit un argument en ligne de commande** (`php cantine.php 3`) :

```php
$numero = (int) ($argv[1] ?? 0);
$jour = Jour::tryFrom($numero);   // tryFrom : renvoie null si invalide, PAS d'exception

if ($jour === null) {
    echo "Jour invalide (attendu : 1 à 7)" . PHP_EOL;
} else {
    echo "{$jour->name} : {$jour->menu()}" . PHP_EOL;
}
```

Affichez ensuite le menu complet de la semaine en bouclant sur `Jour::cases()`, avec un 🔒 devant les jours fermés.

**À retenir** (notez-le en commentaire) : `from()` lève une `ValueError` → pour une donnée **interne** que vous contrôlez ; `tryFrom()` renvoie `null` → pour une donnée venue de l'**extérieur** (URL, formulaire, `argv`).

</details>

<details>
<summary><b>Bonus 4 — Avant / après : la chasse aux chaînes magiques</b></summary>

**1. La version « à ne pas faire »** — tapez ce code et cherchez-lui **au moins trois** défauts :

```php
function degatsSort(string $element): int
{
    if ($element == 'feu')    return 30;
    if ($element == 'glace')  return 20;
    if ($element == 'foudre') return 25;
    return 0;
}

echo degatsSort('feu');     // 30
echo degatsSort('Feu');     // 0  — pourquoi c'est un problème ?
echo degatsSort('foudr');   // 0  — la typo passe inaperçue
echo degatsSort('banane');  // 0  — n'importe quoi est accepté
```

**2. La version enum** — remplacez tout ça par :

```php
enum Element: string
{
    case Feu = 'feu';
    case Glace = 'glace';
    case Foudre = 'foudre';

    public function degats(): int { /* match : 30 / 20 / 25 */ }
    public function emoji(): string { /* 🔥 / ❄️ / ⚡ */ }
    public function faiblesse(): self { /* Feu→Glace, Glace→Foudre, Foudre→Feu */ }
}
```

**3. Montrez que la typo devient impossible :**

```php
Element::from('foudr');   // ValueError immédiate et explicite
// et dans l'éditeur : Element::Foudr est souligné AVANT même de lancer le code
```

**4. Le triangle des éléments** : bouclez sur `Element::cases()` × `Element::cases()` et affichez, pour chaque paire, qui bat qui (en comparant `$b->faiblesse() === $a`).

Concluez, en commentaire, sur ce que l'enum a apporté : validation gratuite, liste des valeurs visible, autocomplétion, `match` sans `default` qui force à traiter les nouveaux cas, comportement rangé **avec** la donnée.

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
