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
<summary><b>Bonus 1 — Le tournoi</b> (interface, polymorphisme, code générique)</summary>

Une **interface** est un contrat : elle liste des méthodes qu'une classe s'engage à fournir, sans dire comment. Objectif : écrire **une** fonction `duel()` capable de faire combattre **n'importe quels** combattants, présents ou futurs.

**1. L'interface**

```php
interface Combattant
{
    public function attaquer(Combattant $cible): void;
    public function recevoirDegats(int $degats): void;
    public function estVivant(): bool;
    public function getNom(): string;
}
```

**2. Trois classes qui l'implémentent**, aux styles volontairement très différents :

| Classe | PV | `attaquer()` |
|--------|----|--------------|
| `Chevalier` | 100 | inflige `random_int(8, 15)` dégâts à chaque coup |
| `Magicien` | 80 | une fois sur deux (`random_int(0, 1)`) le sort **rate** et ne fait rien ; sinon `random_int(25, 40)` dégâts |
| `Poule` | 500 | inflige **1** dégât par coup — la stratégie de l'usure 🐔 |

Pour chacune : `recevoirDegats()` retire les dégâts des PV sans jamais passer sous `0` (`max(0, …)`), `estVivant()` renvoie `$pv > 0`.

**3. La fonction générique**

```php
function duel(Combattant $a, Combattant $b): Combattant
{
    // tant que les deux sont vivants : $a attaque $b, puis si $b survit, $b attaque $a.
    // renvoie le combattant encore debout.
}
```

Elle ne mentionne **aucune** classe concrète, seulement `Combattant`. Testez-la avec un mini-tournoi à quatre (deux demi-finales + une finale), puis affichez le champion.

> 💪 **Pour aller plus loin** : lancez 1000 duels « combattants neufs » entre chaque paire et affichez le pourcentage de victoires. Surprise : **la poule perd toujours** contre le chevalier. Faites le calcul (dégâts/tour × nombre de tours pour vider l'adversaire), puis rééquilibrez la poule pour qu'elle gagne ~50 % du temps. C'est du game design.

</details>

<details>
<summary><b>Bonus 2 — Les super-pouvoirs en kit</b> (traits, constantes de trait, conflits)</summary>

Une classe n'a qu'un **seul** parent, mais peut utiliser autant de **traits** qu'elle veut. Un trait est un bloc de méthodes (et de propriétés) « copié-collé intelligemment » dans plusieurs classes sans lien de parenté.

**1. Trois traits**

- **`Vole`** : contient une constante `public const int ALTITUDE_MAX = 10000;` (PHP 8.2 autorise les constantes dans les traits) et une méthode `voler(): string` qui renvoie `"🕊️  Smaug s'envole (plafond : 10000 m)"` — elle utilise `$this->nom`, en supposant que la classe utilisatrice a une propriété `$nom`.
- **`CracheDuFeu`** : une méthode `cracherDuFeu(): string`.
- **`Invisible`** : une propriété privée `bool $visible = true` et une méthode `disparaitre(): string` qui **bascule** l'état à chaque appel et renvoie un message différent selon qu'on disparaît ou qu'on réapparaît.

**2. Des classes qui composent ces traits**

```php
class Dragon      { use Vole, CracheDuFeu; public function __construct(public string $nom) {} }
class Fantome     { use Vole, Invisible;   public function __construct(public string $nom) {} }
class Politicien  { use Invisible;          public function __construct(public string $nom) {} }
```

Testez : un dragon vole **et** crache du feu ; un fantôme vole **et** disparaît/réapparaît.

**3. Le conflit de noms**

Créez deux traits `Nageur` et `Coureur` qui définissent **tous les deux** une méthode `seDeplacer()`. Une classe `Triathlete` qui fait `use Nageur, Coureur;` **ne compile pas** : PHP refuse de choisir. Résolvez avec `insteadof` et `as` :

```php
class Triathlete
{
    use Nageur, Coureur {
        Nageur::seDeplacer insteadof Coureur;  // en cas de conflit, Nageur gagne
        Coureur::seDeplacer as courir;         // l'autre version reste dispo sous un alias
    }
}
```

**Question de réflexion** à noter dans un commentaire : pourquoi des traits plutôt que `class Dragon extends Volant` ? (Indice : « un dragon *est un* volant » est faux ; il *sait* voler. Et PHP n'a qu'un seul parent.)

</details>

<details>
<summary><b>Bonus 3 — Le contrôle à l'entrée du zoo</b> (plusieurs interfaces, <code>instanceof</code>)</summary>

Une même classe peut implémenter **plusieurs** interfaces : c'est le contournement de l'absence d'héritage multiple.

**1. Deux interfaces**

```php
interface Dangereux  { public function niveauDeMenace(): int; }      // de 1 à 5
interface Caressable { public function reactionAuxCalins(): string; }
```

**2. Une classe abstraite `Animal`** avec `protected string $nom`, une méthode abstraite `crier(): string`, et `getNom()`.

**3. Trois animaux**

| Classe | hérite de | implémente |
|--------|-----------|------------|
| `Lion` | `Animal` | `Dangereux` (menace 5) |
| `Lapin` | `Animal` | `Caressable` |
| `Ours` | `Animal` | `Dangereux` (menace 4) **et** `Caressable` |

Marquez chaque méthode redéfinie avec `#[\Override]`.

**4. La fonction d'inspection**

```php
function inspecterEnclos(array $animaux): void
{
    // pour chaque animal :
    //   s'il est instanceof Dangereux → afficher "⚠️ DANGER niveau X/5"
    //   s'il est instanceof Caressable → afficher "🤗 caresses OK — « <réaction> »"
    //   s'il n'est ni l'un ni l'autre → "😐 rien à signaler"
}
```

Puis une fonction `visiteEnfants(array $animaux): array` qui, avec `array_filter`, ne garde que les animaux `Caressable` **et pas** `Dangereux` (donc : le lapin oui, l'ours non).

**5. L'expérience à faire** : dans `Lion`, renommez `crier` en `crierr` **en gardant `#[\Override]`**. Constatez que PHP refuse de charger le fichier (`has #[\Override] attribute, but no matching parent method exists`) — le filet de sécurité a fonctionné.

</details>

<details>
<summary><b>Bonus 4 — La dynastie royale</b> (chaîne de <code>parent::</code>, <code>final</code>, récursion infinie)</summary>

**1. Trois classes en cascade**

```php
class Monarque {
    public function __construct(protected string $nom) {}
    public function titreComplet(): string { return 'Sa Majesté'; }
}

class Roi extends Monarque {
    // redéfinit titreComplet() : renvoie  parent::titreComplet() . " le Roi {$this->nom}"
}

class RoiSoleil extends Roi {
    // redéfinit titreComplet() : renvoie  parent::titreComplet() . ', lumière de la POO'
}
```

Testez : `(new RoiSoleil('Louis'))->titreComplet()` doit afficher
`Sa Majesté le Roi Louis, lumière de la POO`.

Dans un commentaire, décrivez la chaîne d'appels pas à pas : `RoiSoleil` appelle `Roi` qui appelle `Monarque`, puis chacun ajoute sa part **au retour**.

**2. `final`** : ajoutez `final` devant `Roi::titreComplet()`. Constatez que `RoiSoleil` ne compile plus (`Cannot override final method`). Retirez-le. Notez dans un commentaire *quand* on voudrait vraiment ça (un calcul de prix, une vérif de sécurité qu'aucun enfant ne doit pouvoir contourner).

**3. Le piège** : que se passe-t-il si on écrit `$this->titreComplet()` au lieu de `parent::titreComplet()` dans `RoiSoleil` ? Réponse : `$this->` repart **toujours** de la classe réelle de l'objet → la méthode s'appelle elle-même → récursion infinie → `Maximum function nesting level reached` ou crash mémoire.

Démontrez-le **avec un garde-fou** : une classe `RoiEtourdi` avec un compteur `$profondeur` qui renvoie un message d'arrêt au-delà de 5 appels au lieu de faire planter la machine.

**Règle à retenir** : `parent::` = « la version juste au-dessus de moi dans le code » ; `$this->` = « la version la plus spécialisée de l'objet réel ».

</details>

<details>
<summary><b>Bonus 5 — Le compteur de population</b> (propriété et méthode statiques, constante <code>final</code> typée)</summary>

Créez une classe `Tamagotchi` qui **compte toutes les créatures existantes**, où qu'elles soient.

- `private static int $population = 0;` — **une seule** case mémoire, partagée par toute la classe.
- `final public const int SEUIL_SURPOPULATION = 10;` — constante typée (PHP 8.3) et finale (PHP 8.1 : aucun enfant ne peut la redéfinir).
- `private int $faim = 50;`, `private int $energie = 50;` — l'état propre à chaque créature.
- Constructeur `public function __construct(private string $nom)` qui fait `self::$population++;`.
- `fuguer(): void` — fait `self::$population--;` et affiche un message.
- `public static function recensement(): string` — renvoie `"🌍 Population mondiale de Tamagotchis : N"`, et **si** `self::$population > self::SEUIL_SURPOPULATION`, ajoute un bloc d'alerte surpopulation. ⚠️ **Pas de `$this`** dans une méthode statique : il n'y a pas d'objet courant.
- `public static function getPopulation(): int`.

**Programme de test :**

```php
echo Tamagotchi::recensement() . PHP_EOL;      // appelée SANS aucune instance : 0

$creatures = [];
foreach (['Pixel','Bit','Octet','Byte','Nibble','Flop','Cache','Buffer'] as $n) {
    $creatures[] = new Tamagotchi($n);
}
echo Tamagotchi::recensement() . PHP_EOL;      // 8

foreach (['Kilo','Mega','Giga','Tera','Peta'] as $n) {
    $creatures[] = new Tamagotchi($n);
}
echo Tamagotchi::recensement() . PHP_EOL;      // 13 → ALERTE SURPOPULATION

$creatures[0]->fuguer();
$creatures[1]->fuguer();
echo Tamagotchi::recensement() . PHP_EOL;      // 11
```

Vérifiez enfin qu'une classe enfant qui tente `public const int SEUIL_SURPOPULATION = 999;` provoque bien une erreur fatale (constante `final`).

</details>

<details>
<summary><b>Bonus 6 — Quiz « compilera, compilera pas ? »</b></summary>

Pour chaque extrait : dites s'il **compile et s'exécute** (et ce qu'il affiche) ou s'il provoque une **erreur fatale** (laquelle). Écrivez votre réponse, puis vérifiez.

**Extrait 1**

```php
abstract class A { abstract public function go(): string; }
$a = new A();
```

**Extrait 2**

```php
class B { protected function secret(): string { return 'chut'; } }
class C extends B { public function espionner(): string { return $this->secret(); } }
echo (new C())->espionner();
```

**Extrait 3**

```php
class D { public function hello(): string { return 'bonjour'; } }
class E extends D { protected function hello(): string { return 'salut'; } }
```

**Extrait 4**

```php
final class F {}
class G extends F {}
```

**Extrait 5**

```php
class H { private string $tresor = 'or'; }
class I extends H { public function voler(): string { return $this->tresor; } }
echo (new I())->voler();
```

Notions testées : classe abstraite non instanciable, `protected` accessible depuis l'enfant, on n'ampute jamais une visibilité en redéfinissant, `final` bloque l'héritage, et surtout : depuis l'enfant, une propriété `private` du parent est **invisible** (Warning + `null`), pas « inaccessible ». En écriture, `$this->tresor = …` depuis l'enfant crée une **propriété fantôme distincte** — le bug parfait, et la raison de la dépréciation des propriétés dynamiques en PHP 8.2.

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
