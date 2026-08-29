# Chapitre 2 — L'héritage

> 📖 Cours : [2. L'héritage](../README.md#2-lhéritage)

Comme au chapitre 1, ces exercices se suivent. **Gardez le même fichier** `animaux.php`.

---

## 🐾 Exercice 2.1 — Le code en double ⭐

Recopiez ce fichier et lancez-le :

```php
<?php

class Chien
{
    public function __construct(public string $nom) {}

    public function manger(): string
    {
        return $this->nom . ' mange.';
    }

    public function crier(): string
    {
        return $this->nom . ' fait : Wouf !';
    }
}

class Chat
{
    public function __construct(public string $nom) {}

    public function manger(): string
    {
        return $this->nom . ' mange.';
    }

    public function crier(): string
    {
        return $this->nom . ' fait : Miaou !';
    }
}

echo (new Chien('Rex'))->manger() . PHP_EOL;
echo (new Chat('Félix'))->crier() . PHP_EOL;
```

**Questions** (répondez en commentaire) :

1. Quelle méthode est écrite **exactement pareil** dans les deux classes ?
2. Si demain vous devez corriger cette méthode, combien de fois faudra-t-il le faire ?
3. Et avec 10 animaux différents ?

💡 C'est ce problème que l'héritage résout.

---

## 👨‍👦 Exercice 2.2 — Votre premier `extends` ⭐

**À faire :** remplacez tout le contenu du fichier par ceci :

```php
<?php

// La classe PARENTE : ce que TOUS les animaux ont en commun
class Animal
{
    public function __construct(public string $nom) {}

    public function manger(): string
    {
        return $this->nom . ' mange.';
    }
}

// La classe ENFANT : elle hérite de tout ce qui est au-dessus
class Chien extends Animal
{
    public function crier(): string
    {
        return $this->nom . ' fait : Wouf !';
    }
}

$rex = new Chien('Rex');

echo $rex->manger() . PHP_EOL;   // méthode HÉRITÉE d'Animal
echo $rex->crier() . PHP_EOL;    // méthode PROPRE à Chien
```

**Résultat attendu :**

```text
Rex mange.
Rex fait : Wouf !
```

💡 **À retenir** : `Chien extends Animal` veut dire « un Chien **est un** Animal ». Il récupère automatiquement le constructeur et la méthode `manger()`, sans les réécrire.

---

## 🐈 Exercice 2.3 — Une deuxième classe enfant ⭐

**À faire :**

1. Ajoutez une classe `Chat extends Animal`, avec une méthode `crier()` qui renvoie `Félix fait : Miaou !`.
2. Créez un chat nommé `Félix`, faites-le manger et crier.

**Résultat attendu :**

```text
Rex mange.
Rex fait : Wouf !
Félix mange.
Félix fait : Miaou !
```

**Question** : combien de fois avez-vous écrit la méthode `manger()` en tout ? Comparez avec l'exercice 2.1.

---

## 🔁 Exercice 2.4 — La boucle magique ⭐

C'est l'intérêt principal de l'héritage : traiter des objets différents de la même façon.

**À faire :** ajoutez ceci à la fin de votre fichier :

```php
$animaux = [
    new Chien('Rex'),
    new Chat('Félix'),
    new Chien('Médor'),
];

foreach ($animaux as $animal) {
    echo $animal->crier() . PHP_EOL;
}
```

**Résultat attendu :**

```text
Rex fait : Wouf !
Félix fait : Miaou !
Médor fait : Wouf !
```

💡 **Regardez bien** : une seule boucle, et chaque animal crie à SA façon. Vous n'avez écrit aucun `if`. Ça s'appelle le **polymorphisme**, et c'est la raison d'être de l'héritage.

---

## 📝 Exercice 2.5 — Redéfinir une méthode ⭐⭐

Un enfant peut **remplacer** une méthode de son parent.

**À faire :** ajoutez cette méthode dans la classe `Chat` :

```php
    public function manger(): string
    {
        return $this->nom . ' mange délicatement, en laissant la moitié.';
    }
```

Relancez `echo $felix->manger();`

**Résultat attendu :**

```text
Félix mange délicatement, en laissant la moitié.
```

Le chien, lui, garde la version du parent.

**Bonus** : ajoutez `#[\Override]` juste au-dessus de la méthode. C'est une sécurité de PHP 8.3 : essayez de mal écrire le nom (`mangerr`) et regardez PHP vous arrêter net.

---

## ☝️ Exercice 2.6 — Réutiliser le parent avec `parent::` ⭐⭐

Parfois on ne veut pas *remplacer* le parent, mais *compléter* ce qu'il fait.

**À faire :** modifiez la méthode `manger()` du `Chat` :

```php
    public function manger(): string
    {
        return parent::manger() . ' Puis il fait sa toilette.';
    }
```

**Résultat attendu :**

```text
Félix mange. Puis il fait sa toilette.
```

💡 `parent::manger()` exécute la version du parent, et on ajoute notre texte après.

---

## 🚫 Exercice 2.7 — La classe abstraite ⭐⭐

Problème : on peut écrire `new Animal('Truc')`. Mais un « animal » tout court, ça ne crie pas — ça n'existe pas vraiment.

**À faire :**

1. Ajoutez le mot `abstract` devant `class Animal`.
2. Ajoutez cette ligne dans `Animal` (une méthode **sans corps**, qui oblige les enfants à l'écrire) :

```php
    abstract public function crier(): string;
```

3. Essayez `new Animal('Truc');` → lisez l'erreur.
4. Vérifiez que `Chien` et `Chat` fonctionnent toujours.

💡 **À retenir** : `abstract` sur la classe = « on ne peut pas l'instancier ». `abstract` sur une méthode = « chaque enfant DOIT l'écrire ».

**Test** : créez une classe `Poisson extends Animal` **sans** méthode `crier()`. PHP refuse. Ajoutez-la (les poissons font `Blub.`) et tout rentre dans l'ordre.

---

## 🦜 Exercice 2.8 — Tout seul, maintenant ⭐⭐

Ajoutez un `Perroquet` à votre ménagerie :

- il hérite d'`Animal` ;
- son constructeur reçoit **deux** paramètres : le nom et une phrase ;
- sa méthode `crier()` répète la phrase deux fois, séparée par `Croâ !`.

💡 **Coup de pouce** pour le constructeur à deux paramètres :

```php
    public function __construct(string $nom, private string $phrase)
    {
        parent::__construct($nom);   // on laisse le parent gérer le nom
    }
```

**Résultat attendu :**

```text
Coco fait : À l'abordage ! Croâ ! À l'abordage !
```

Ajoutez-le au tableau `$animaux` et relancez la boucle : il s'intègre sans rien changer d'autre. 🎉

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus 1 — Les interfaces (le tournoi)</b></summary>

Une **interface** est un contrat : elle liste des méthodes qu'une classe s'engage à fournir, sans dire comment.

Créez `interface Combattant` avec `attaquer()`, `recevoirDegats()` et `estVivant()`, puis trois classes très différentes qui l'implémentent (Chevalier, Magicien, Poule). Écrivez une fonction `duel(Combattant $a, Combattant $b)` qui les fait combattre — elle marchera avec n'importe quelle classe respectant le contrat.

Corrigé : [`02b-tournoi.php`](../solutions/chapitre-02/02b-tournoi.php)

</details>

<details>
<summary><b>Bonus 2 — Les traits (les super-pouvoirs en kit)</b></summary>

Une classe n'a qu'un seul parent, mais peut utiliser autant de **traits** qu'elle veut. Créez `Vole`, `CracheDuFeu`, `Invisible`, puis `Dragon` (vole + crache), `Fantome` (vole + invisible).

Corrigé : [`02c-traits.php`](../solutions/chapitre-02/02c-traits.php)

</details>

<details>
<summary><b>Bonus 3 — Les autres exercices avancés</b></summary>

- **Le contrôle au zoo** : deux interfaces sur une même classe, `instanceof` — [`02e-zoo.php`](../solutions/chapitre-02/02e-zoo.php)
- **La dynastie royale** : chaîne de `parent::`, et le piège de la récursion infinie — [`02f-dynastie.php`](../solutions/chapitre-02/02f-dynastie.php)
- **Le compteur de population** : propriétés et méthodes statiques — [`02d-recensement.php`](../solutions/chapitre-02/02d-recensement.php)
- **Quiz** « compilera, compilera pas ? » — [`02g-quiz.php`](../solutions/chapitre-02/02g-quiz.php)

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
