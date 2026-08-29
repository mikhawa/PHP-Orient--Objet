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
<summary><b>Bonus 3 — Les exercices avancés</b></summary>

Ces exercices supposent le chapitre 1 bien digéré :

- **Le dé parlant** : fabriques statiques et `random_int()` — corrigé : [`01d-de-parlant.php`](../solutions/chapitre-01/01d-de-parlant.php)
- **La fabrique de super-héros** : arguments nommés et `readonly` — corrigé : [`01b-super-heros.php`](../solutions/chapitre-01/01b-super-heros.php)
- **Le coffre-fort** : trois essais puis blocage définitif — corrigé : [`01c-coffre-fort.php`](../solutions/chapitre-01/01c-coffre-fort.php)
- **La jauge magique** : property hooks de PHP 8.4 — corrigé : [`01e-jauge.php`](../solutions/chapitre-01/01e-jauge.php)
- **Le quiz** « que va afficher ce code ? » — corrigé : [`01f-quiz.php`](../solutions/chapitre-01/01f-quiz.php)

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
