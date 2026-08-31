# Chapitre 1 — Les classes et les objets

> 📖 Cours : [1. Les classes et les objets](../README.md#1-les-classes-et-les-objets)

Ces exercices se suivent : **gardez le même fichier** et enrichissez-le au fur et à mesure. À la fin, vous aurez écrit une vraie classe, étape par étape.

---

## 🐱 Exercice 1.1 — Votre première classe ⭐

Créez un fichier `chat.php` et recopiez ceci :

```php
<?php

class Chat
{
    public string $nom = 'Sans nom';
    public int $age = 0;
}

$chat = new Chat();

echo $chat->nom . PHP_EOL;
echo $chat->age . PHP_EOL;
```

**À faire :**

1. Lancez le fichier. Vous devez voir `Sans nom` et `0`.
2. Après le `new`, donnez à votre chat le nom `Félix` et l'âge `3`.
3. Relancez.

**Résultat attendu :**

```text
Félix
3
```

---

## 💬 Exercice 1.2 — Votre première méthode ⭐

Une **méthode**, c'est une fonction écrite *à l'intérieur* d'une classe.

**À faire :** ajoutez cette méthode dans la classe `Chat`, juste après les propriétés :

```php
    public function miauler(): string
    {
        return 'Miaou !';
    }
```

Puis, en dehors de la classe :

```php
echo $chat->miauler() . PHP_EOL;
```

**Résultat attendu :**

```text
Félix
3
Miaou !
```

💡 Notez les parenthèses : `$chat->nom` est une **propriété** (pas de parenthèses), `$chat->miauler()` est une **méthode** (avec parenthèses).

---

## 🪞 Exercice 1.3 — Le mot-clé `$this` ⭐

Une méthode peut lire les propriétés de son propre objet grâce à `$this`.

**À faire :** ajoutez cette méthode à la classe `Chat` :

```php
    public function sePresenter(): string
    {
        return 'Je suis ' . $this->nom . ' et j\'ai ' . $this->age . ' ans.';
    }
```

Appelez-la :

```php
echo $chat->sePresenter() . PHP_EOL;
```

**Résultat attendu :**

```text
Je suis Félix et j'ai 3 ans.
```

**Question** : que se passe-t-il si vous écrivez `$nom` au lieu de `$this->nom` dans la méthode ? Essayez, lisez l'erreur.

💡 **À retenir** : à l'intérieur d'une classe, `$this` veut dire « l'objet en cours ». Sans lui, PHP cherche une variable locale qui n'existe pas.

---

## 🏗️ Exercice 1.4 — Le constructeur ⭐

Écrire `$chat->nom = ...` puis `$chat->age = ...` à chaque fois, c'est fastidieux. Le **constructeur** permet de tout faire en une ligne.

**À faire :**

1. Ajoutez cette méthode dans la classe `Chat` :

```php
    public function __construct(string $nom, int $age)
    {
        $this->nom = $nom;
        $this->age = $age;
    }
```

2. Remplacez vos trois lignes de création par une seule :

```php
$chat = new Chat('Félix', 3);
```

3. Créez un deuxième chat sur une seule ligne : `Grosminet`, 5 ans. Faites-le se présenter.

**Résultat attendu :**

```text
Je suis Félix et j'ai 3 ans.
Je suis Grosminet et j'ai 5 ans.
```

---

## ✨ Exercice 1.5 — La version courte du constructeur ⭐

PHP 8 propose un raccourci : si vous mettez `public` devant les paramètres du constructeur, PHP crée les propriétés **tout seul**.

**À faire :** remplacez le début de votre classe par ceci — et **supprimez** les deux lignes `public string $nom` et `public int $age` du dessus :

```php
class Chat
{
    public function __construct(
        public string $nom,
        public int $age,
    ) {}

    // ... vos méthodes miauler() et sePresenter() ne changent pas
}
```

Relancez : **le résultat doit être exactement le même qu'avant.**

💡 On appelle ça la **promotion de propriétés**. C'est la façon d'écrire un constructeur en PHP moderne : vous la verrez partout.

---

## 🔒 Exercice 1.6 — Protéger une propriété ⭐⭐

Problème : n'importe qui peut écrire `$chat->age = -50;`. Un chat de −50 ans, ça n'existe pas.

**À faire :**

1. Dans le constructeur, remplacez `public int $age` par `private int $age`.
2. Lancez : `sePresenter()` fonctionne toujours (on est *dans* la classe), mais si vous essayez `echo $chat->age;` depuis l'extérieur, vous avez une erreur. Vérifiez-le.
3. Pour pouvoir quand même lire l'âge de l'extérieur, ajoutez un **getter** :

```php
    public function getAge(): int
    {
        return $this->age;
    }
```

4. Affichez `echo $chat->getAge();`

**Résultat attendu :**

```text
3
```

💡 **À retenir** : `private` = accessible seulement à l'intérieur de la classe. Un `getter` est une méthode publique qui donne accès en lecture.

---

## 🎂 Exercice 1.7 — Le setter qui vérifie ⭐⭐

Le vrai intérêt de `private` : contrôler ce qu'on écrit.

**À faire :** ajoutez cette méthode :

```php
    public function setAge(int $age): void
    {
        if ($age < 0) {
            echo '⛔ Un âge négatif ? Non. On garde ' . $this->age . '.' . PHP_EOL;
            return;
        }

        $this->age = $age;
    }
```

Testez :

```php
$chat->setAge(4);
echo $chat->getAge() . PHP_EOL;   // 4

$chat->setAge(-50);               // refusé !
echo $chat->getAge() . PHP_EOL;   // toujours 4
```

**Résultat attendu :**

```text
4
⛔ Un âge négatif ? Non. On garde 4.
4
```

💡 **Voilà pourquoi on met les propriétés en `private`** : l'objet devient responsable de ses propres données. Personne ne peut lui mettre n'importe quoi.

---

## 🐣 Exercice 1.8 — Le mini-Tamagotchi ⭐⭐

Maintenant, tout seul (mais vous avez tout vu ci-dessus).

Créez une classe `Tamagotchi` avec :

- une propriété **privée** `$nom` (string) et une propriété **privée** `$faim` (int, qui vaut `50` au départ) ;
- un constructeur qui reçoit uniquement le nom ;
- une méthode `manger()` qui **enlève 20** à la faim ;
- une méthode `jouer()` qui **ajoute 15** à la faim ;
- une méthode `etat()` qui renvoie `"🐣 Pixel a une faim de 50/100"`.

**Programme de test :**

```php
$pixel = new Tamagotchi('Pixel');
echo $pixel->etat() . PHP_EOL;

$pixel->manger();
echo $pixel->etat() . PHP_EOL;

$pixel->jouer();
echo $pixel->etat() . PHP_EOL;
```

**Résultat attendu :**

```text
🐣 Pixel a une faim de 50/100
🐣 Pixel a une faim de 30/100
🐣 Pixel a une faim de 45/100
```

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus 1 — Le Tamagotchi qui reste entre 0 et 100</b></summary>

Ajoutez une méthode privée qui empêche la faim de sortir de l'intervalle `[0, 100]` :

```php
    private function borner(int $valeur): int
    {
        return max(0, min(100, $valeur));
    }
```

Utilisez-la dans `manger()` et `jouer()`. Vérifiez : après 10 `jouer()` d'affilée, la faim doit s'arrêter à 100, pas monter à 200.

</details>

<details>
<summary><b>Bonus 2 — La méthode magique __toString()</b></summary>

Renommez `etat()` en `__toString()`. Vous pouvez alors écrire directement :

```php
echo $pixel . PHP_EOL;    // sans appeler de méthode !
```

PHP appelle `__toString()` automatiquement dès qu'on utilise l'objet comme une chaîne.

</details>

<details>
<summary><b>Bonus 3 — Le dé parlant</b> (fabriques statiques, <code>random_int()</code>, <code>__toString()</code>)</summary>

Vous préparez un petit moteur de jeu de rôle. Créez une classe `De` (un dé) :

- une propriété **`public readonly int $faces`**, initialisée par le constructeur (valeur par défaut : `6`). Elle est `readonly` : le nombre de faces d'un dé ne change jamais après sa fabrication ;
- une propriété **privée** `?int $dernierLancer` valant `null` au départ ;
- une méthode **`lancer(): int`** qui tire un nombre entre `1` et `$faces` avec **`random_int()`** (et non `rand()` : `random_int()` est un vrai générateur aléatoire non prévisible), le **stocke** dans `$dernierLancer`, puis le renvoie ;
- une méthode magique **`__toString(): string`** qui renvoie :
  - `"🎲 Dé à 6 faces (jamais lancé)"` tant que `$dernierLancer` vaut `null` ;
  - `"🎲 Dé à 6 faces → 4"` une fois le dé lancé.

Ajoutez ensuite **deux fabriques statiques** — des méthodes `static` qui renvoient un objet déjà configuré, avec le type de retour `static` :

```php
public static function classique(): static { /* un dé à 6 faces */ }
public static function deDonjon(): static { /* un dé à 20 faces */ }
```

**Programme de test :**

```php
$d6  = De::classique();
$d20 = De::deDonjon();

echo $d6 . PHP_EOL;        // pas encore lancé
$d6->lancer();
echo $d6 . PHP_EOL;        // affiche le résultat

echo 'Initiative : ' . $d20->lancer() . PHP_EOL;
```

**Résultat attendu (les nombres varient) :**

```text
🎲 Dé à 6 faces (jamais lancé)
🎲 Dé à 6 faces → 4
Initiative : 17
```

> 💪 **Pour aller encore plus loin** : ajoutez `lancerAvantage(): int` qui lance **deux fois** le dé et garde le meilleur résultat (le « jet avec avantage » des rôlistes), puis organisez un *best of 5* entre le `d6` et le `d20` en comptant les manches gagnées.

</details>

<details>
<summary><b>Bonus 4 — La fabrique de super-héros</b> (arguments nommés, <code>readonly</code>)</summary>

Créez une classe `SuperHeros` dont le constructeur utilise la **promotion de propriétés** avec **quatre paramètres**, dont trois ont une valeur par défaut :

| Paramètre | Type | Défaut |
|-----------|------|--------|
| `$nom` | `string` | *(obligatoire)* |
| `$pouvoir` | `string` | `'aucun'` |
| `$couleurCape` | `string` | `'rouge'` |
| `$porteSlipParDessus` | `bool` | `false` |

Ajoutez une propriété **`public readonly string $identiteSecrete`** qui **n'est pas** un paramètre : calculez-la **dans le corps du constructeur**, à partir du nom, en inversant les lettres du nom passé en minuscules et sans espaces :

```php
$this->identiteSecrete = strrev(strtolower(str_replace(' ', '', $nom)));
```

Écrivez enfin `sePresenter(): string` qui renvoie une phrase du type :
`"Je suis Slipman, mon pouvoir est aucun, ma cape est à pois, et OUI je porte mon slip par-dessus mon costume !"`
(la fin de phrase dépend de `$porteSlipParDessus`).

**Programme de test** — instanciez **quatre héros** en utilisant à chaque fois une forme différente d'appel :

```php
$heros = [
    new SuperHeros('Capitaine Gaufre', 'de lancer du sucre', 'dorée', true), // tout dans l'ordre
    new SuperHeros('Madame Sieste', pouvoir: 'de dormir 19 h par jour'),      // un seul argument nommé
    new SuperHeros(porteSlipParDessus: true, nom: 'Slipman', couleurCape: 'à pois'), // ordre libre
    new SuperHeros('Monsieur Rien'),                                          // que des défauts
];

foreach ($heros as $h) {
    echo '🦸 ' . $h->sePresenter() . PHP_EOL;
}
```

Puis prouvez que la propriété est bien verrouillée :

```php
echo $heros[0]->identiteSecrete . PHP_EOL;   // lecture publique : OK
try {
    $heros[0]->identiteSecrete = 'BruceWayne';
} catch (Error $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL; // Cannot modify readonly property
}
```

</details>

<details>
<summary><b>Bonus 5 — Le coffre-fort</b> (encapsulation d'un état sensible)</summary>

Un coffre-fort protège son contenu **et** son code. Toute la logique passe par des méthodes publiques : de l'extérieur, impossible de forcer l'état interne.

Créez une classe `CoffreFort` avec ces propriétés **toutes privées** :

- `array $contenu = []` — les objets rangés ;
- `bool $verrouille = true` — un coffre est fermé par défaut ;
- `int $echecs = 0` — le nombre de codes faux consécutifs ;
- `bool $bloqueAJamais = false` — devient `true` après trop d'échecs ;
- une constante `private const int MAX_ESSAIS = 3`.

Le constructeur reçoit le code : `public function __construct(private string $code) {}`.

**Méthodes :**

- **`deverrouiller(string $tentative): bool`**
  - si le coffre est bloqué à jamais : message d'erreur, renvoie `false` ;
  - si `hash_equals($this->code, $tentative)` est vrai : déverrouille, **remet `$echecs` à 0**, renvoie `true` ;
  - sinon : incrémente `$echecs`. S'il ne reste plus d'essai, passe `$bloqueAJamais` à `true`. Sinon, affiche le nombre d'essais restants. Renvoie `false`.
  - *(pourquoi `hash_equals` et pas `===` ? C'est la bonne pratique pour comparer un secret : le temps de comparaison ne dépend pas du nombre de caractères justes, ce qui évite une « attaque temporelle ».)*
- **`deposer(string $objet): void`** — ajoute au contenu, **seulement si le coffre est accessible** (déverrouillé et non bloqué) ;
- **`ouvrir(): array`** — affiche et renvoie le contenu, même condition d'accès ;
- **`verrouiller(): void`** — repasse `$verrouille` à `true` ;
- **`private estAccessible(): bool`** — factorise la double vérification (bloqué ? verrouillé ?) utilisée par `deposer()` et `ouvrir()`.

**Scénario de test à reproduire :**

```php
$coffre = new CoffreFort('1789');

$coffre->deverrouiller('1789');
$coffre->deposer("Lingot d'or");
$coffre->deposer('Recette secrète des gaufres');
$coffre->verrouiller();

// La nuit, un cambrioleur essaie plusieurs codes :
$coffre->ouvrir();              // verrouillé → rien
$coffre->deverrouiller('0000'); // faux, 2 essais restants
$coffre->deverrouiller('1234'); // faux, 1 essai restant
$coffre->deverrouiller('4321'); // faux → BLOCAGE DÉFINITIF
$coffre->deverrouiller('1789'); // même le bon code ne marche plus
```

</details>

<details>
<summary><b>Bonus 6 — La jauge magique</b> (property hooks + visibilité asymétrique, PHP 8.4)</summary>

⚠️ **Nécessite PHP 8.4.** Vérifiez avec `php -v`.

Une même classe `Jauge` doit servir pour les points de vie, le mana, une batterie… Elle borne toujours sa valeur entre `0` et un maximum, sans jamais qu'on ait besoin d'écrire un setter.

- Constructeur : `public readonly string $nom`, `public readonly int $max = 100`.
- Propriété **`public int $valeur`** dotée d'un **hook `set`** : à chaque écriture (`$jauge->valeur = ...`), la valeur reçue est **bornée** à `[0, $max]` avant d'être stockée, et un compteur `$nbModifications` est incrémenté.

  ```php
  public int $valeur = 0 {
      set(int $valeur) {
          $this->valeur = max(0, min($this->max, $valeur));
          $this->nbModifications++;
      }
  }
  ```

- Propriété **virtuelle** `public float $pourcentage` avec un **hook `get`** : elle n'est **jamais stockée**, elle se calcule à la lecture → `round($this->valeur / $this->max * 100, 1)`.
- Propriété **`public private(set) int $nbModifications = 0`** : tout le monde peut la **lire**, seule la classe peut l'**écrire**.
- Méthode `barre(int $cases = 10): string` qui dessine une barre de progression, par exemple :
  `Vie [███████░░░] 70 %` (avec `str_repeat('█', ...)` et `str_repeat('░', ...)`).

Dans le constructeur, initialisez `$this->valeur = $max;` (cela passe par le hook) puis **remettez `$nbModifications` à 0** pour ne pas compter l'initialisation.

**Programme de test :**

```php
$vie = new Jauge('Vie', 100);
echo $vie->barre() . PHP_EOL;          // Vie [██████████] 100 %

$vie->valeur = 130;                    // le hook borne à 100
echo $vie->barre() . PHP_EOL;

$vie->valeur -= 65;                    // 100 → 35
echo $vie->barre() . PHP_EOL;
echo 'Pourcentage : ' . $vie->pourcentage . PHP_EOL;

$vie->valeur = -9999;                  // le hook borne à 0
echo $vie->barre() . PHP_EOL;

echo 'Modifications : ' . $vie->nbModifications . PHP_EOL; // lecture OK
try {
    $vie->nbModifications = 0;         // écriture interdite hors de la classe
} catch (Error $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}
```

</details>

<details>
<summary><b>Bonus 7 — Le quiz « que va afficher ce code ? »</b></summary>

Pour **chaque** extrait ci-dessous : écrivez d'abord sur papier ce que la dernière ligne va afficher (ou l'erreur qu'elle va provoquer), **puis** tapez le code pour vérifier. L'important est de savoir **expliquer pourquoi**.

**Extrait 1**

```php
class Boite { public string $contenu = 'vide'; }
$a = new Boite();
$b = $a;
$b->contenu = 'trésor';
echo $a->contenu;   // ?
```

**Extrait 2** (à la suite du 1)

```php
$c = clone $a;
$c->contenu = 'chaussette';
echo $a->contenu;   // ?
```

**Extrait 3**

```php
class Portefeuille {
    public function __construct(public readonly float $solde) {}
}
$p = new Portefeuille(50.0);
$p->solde = 1000000.0;   // ?
```

**Extrait 4**

```php
class Chrono {
    public static int $tics = 0;
    public function tic(): void { self::$tics++; }
}
$c1 = new Chrono();
$c2 = new Chrono();
$c1->tic();
$c2->tic();
$c2->tic();
echo Chrono::$tics;   // ?
```

**Extrait 5**

```php
class Fantome {}
$f = new Fantome();
$f->boo = 'BOUH';   // ? (que se passe-t-il exactement, depuis PHP 8.2 ?)
```

Les notions testées : référence vs copie, `clone`, `readonly`, propriété `static` partagée, propriétés dynamiques dépréciées.

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
