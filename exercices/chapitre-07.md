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
<summary><b>Bonus 1 — Le Choixpeau magique</b> (<code>match(true)</code>, <code>match</code> vs <code>switch</code>, <code>UnhandledMatchError</code>)</summary>

Objectif : répartir des stagiaires dans quatre « maisons », **sans un seul `if`**.

**1. Une classe `Choixpeau`**

- `repartir(int $courage, int $ruse, int $sagesse, int $loyaute): string` — calcule `$meilleur = max(...)` des quatre scores, puis un **`match(true)`** qui retient la **première** condition vraie :

  ```php
  return match (true) {
      $meilleur === 0        => 'Cafétéria',   // cas particulier EN PREMIER
      $courage === $meilleur => 'GRYFFONDOR',
      $ruse === $meilleur    => 'SERPENTARD',
      $sagesse === $meilleur => 'SERDAIGLE',
      default                => 'POUFSOUFFLE',
  };
  ```

- `commentaire(string $maison): string` — un **`match` classique** (comparaison stricte `===` sur la valeur), **sans `default`**, qui renvoie une phrase par maison.

**2. Répartissez ~5 stagiaires** (dont un « Fantôme » avec 0 partout) en affichant leurs scores et le verdict.

**3. Réécrivez `commentaire()` avec un `switch`** et notez, dans un commentaire, **les 3 différences** :

| | `match` | `switch` |
|---|---|---|
| nature | une **expression** (`$x = match(…)`) | une **instruction** (variable temporaire + `break`) |
| comparaison | stricte `===` | lâche `==` |
| enchaînement | aucun *fallthrough* | `break` oublié = bug classique |

**4. Sans `default`** : appelez `$choixpeau->commentaire('POUDLARD EXPRESS')` et attrapez l'`\UnhandledMatchError`. Constatez qu'un `switch` sans `default`, lui, ne ferait **rien** en silence. C'est une fonctionnalité : le jour où vous ajoutez une 5ᵉ maison, `match` vous **force** à la traiter.

</details>

<details>
<summary><b>Bonus 2 — Le GPS prudent</b> (opérateur nullsafe <code>?-></code>, Warning ≠ Error)</summary>

**1. Trois classes emboîtées**, chacune pouvant manquer :

```php
class Pays  { public function __construct(public readonly string $nom) {} }
class Ville { public function __construct(public readonly string $nom, public readonly ?Pays $pays = null) {} }
class Utilisateur { public function __construct(public readonly string $pseudo, public readonly ?Ville $ville = null) {} }
```

**2. Trois utilisateurs** : Aline (ville + pays), Bob (ville **sans** pays), Carla (**pas** de ville).

**3. `localiser(Utilisateur $u): string` en UNE ligne utile**, avec `?->` :

```php
$pays = $u->ville?->pays?->nom;   // null dès qu'un maillon est null
return $pays === null
    ? "{$u->pseudo} est quelque part dans le multivers 🌀"
    : "{$u->pseudo} est en $pays";
```

**4. Réécrivez la même fonction avec des `if` imbriqués** et comptez les lignes (≈ 7 contre 1). Avec un niveau de profondeur de plus, la version `if` passerait à 10, la version nullsafe resterait à 1.

**5. Le piège, à observer avec un `set_error_handler`** :

- lire `$bob->ville->pays->nom` **sans** `?->` (le pays est `null`) → **Warning** (pas Error !), l'expression vaut `null`, **le script continue** ;
- pour Carla, **deux** warnings en cascade (`->pays` sur `null`, puis `->nom` sur ce `null`) ;
- en revanche, **appeler une méthode** sur `null` (`$carla->ville->getNom()`) est une **`Error` fatale**.

**À retenir** (en commentaire) : `?->` court-circuite tout ce qui est à droite ; il ne marche **pas** à gauche d'une affectation ; et une chaîne `$a?->b?->c?->d?->e` est un signal qu'il faut sans doute corriger le problème **en amont** (valeur par défaut, Null Object…).

</details>

<details>
<summary><b>Bonus 3 — L'usine à gâteaux</b> (arguments nommés, <code>new</code> dans un initialiseur)</summary>

**1. Une classe `Gateau`** avec un constructeur promu à **6 paramètres, tous avec valeur par défaut** :
`base = 'génoise'`, `garniture = 'chantilly'`, `nappage = 'chocolat'`, `bougies = 0`, `messageEcrit = ''`, `sansGluten = false`.

Ajoutez `__toString()` qui construit une description en n'incluant les bougies / le message / la mention sans gluten que s'ils sont renseignés.

**2. Quatre commandes**, chacune avec une forme d'appel différente :

```php
new Gateau();                                                   // le standard
new Gateau(sansGluten: true);                                   // SEULEMENT le 6e paramètre
new Gateau(garniture: 'crème pâtissière', base: 'pâte sablée'); // ordre libre
new Gateau(bougies: 30, messageEcrit: 'Joyeux anniv Aline !', garniture: 'mousse');
```

**3. `new` dans un initialiseur (PHP 8.1)** : une classe `Commande` dont le constructeur a
`private Gateau $gateau = new Gateau()` comme valeur par défaut (avant 8.1, il fallait accepter `null` puis créer l'objet dans le corps). Méthode `ticket(): string`.

**4. La règle des arguments** : montrez que `new Gateau(garniture: 'fraise', 'génoise')` est une **erreur fatale** — un argument **positionnel** ne peut jamais suivre un argument **nommé**. `new Gateau('génoise', garniture: 'fraise')` est correct.

> 💡 Terminez avec quelques exemples d'arguments nommés sur des **fonctions natives** : `array_slice($t, offset: 2, length: 3)`, `in_array($x, $t, strict: true)`, `json_encode($d, flags: JSON_PRETTY_PRINT)`.

</details>

<details>
<summary><b>Bonus 4 — La chaîne de fabrication</b> (difficile : callables de première classe, chaînage fluide)</summary>

Vous construisez un « tuyau » qui fait passer une valeur à travers une suite de transformations.

**1. Une classe `Pipeline`**

```php
class Pipeline
{
    /** @var \Closure[] */
    private array $etapes = [];

    public function ajouter(callable $etape): static
    {
        $this->etapes[] = \Closure::fromCallable($etape);
        return $this;   // ← renvoyer $this permet le chaînage ->ajouter(...)->ajouter(...)
    }

    public function executer(mixed $valeur): mixed
    {
        foreach ($this->etapes as $etape) {
            $valeur = $etape($valeur);
        }
        return $valeur;
    }
}
```

**2. Un nettoyeur de pseudo**, en utilisant la syntaxe **callable de première classe `f(...)`** (PHP 8.1) :

```php
$nettoyeur = (new Pipeline())
    ->ajouter(trim(...))
    ->ajouter(strtolower(...))
    ->ajouter(fn(string $s) => str_replace(' ', '_', $s))
    ->ajouter(fn(string $s) => substr($s, 0, 15));

echo $nettoyeur->executer('   Le Grand Sorcier DU Code   ');  // le_grand_sorcie
```

**3. Pourquoi `strtoupper(...)` plutôt que `'strtoupper'` ?** Démontrez-le : une chaîne `'strtouper'` (avec une typo) ne provoque une erreur **qu'à l'appel** ; la syntaxe `strtouper(...)` est vérifiée **avant** l'exécution. Notez aussi les 3 anciennes syntaxes (`'f'`, `[$obj, 'm']`, `'C::m'`) que `f(...)` unifie.

**4. Bonus PHP 8.5** : si l'opérateur pipe `|>` est dispo, montrez que
`'   Texte   ' |> trim(...) |> strtolower(...)` fait exactement ce que votre `Pipeline` fait à la main — en se lisant de gauche à droite au lieu de `strtolower(trim('   Texte   '))`.

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
