# PHP OO 2026

## PHP 8 Orienté Objet

---

L'orienté objet, en **PHP** et dans d'autres langages, est également nommé **POO** (programmation orientée objet).

C'est un **paradigme** de programmation informatique.

La **POO** consiste en la définition et l'interaction de briques logicielles appelées objets.

Un **objet** représente un concept, une idée ou toute entité du monde physique, comme une voiture, une personne ou encore une page d'un livre. Il possède une **structure** interne et un **comportement**, et il sait interagir avec ses pairs.

Il s'agit donc de représenter ces objets et leurs relations. L'interaction entre les objets via leurs relations permet de concevoir et réaliser les fonctionnalités attendues, de mieux résoudre le ou les problèmes. Dès lors, l'étape de modélisation revêt une importance majeure et nécessaire pour la POO. C'est elle qui permet de transcrire les éléments du réel sous forme virtuelle.

### Prérequis

Ce cours s'adresse à des personnes qui connaissent déjà :

- le **PHP procédural** (variables, fonctions, tableaux, structures de contrôle) ;
- les **requêtes préparées avec PDO**.

Nous réutiliserons ces connaissances tout au long du cours, notamment dans les chapitres consacrés au mapping SQL et aux managers.

### Les versions de PHP 8

Ce cours couvre les apports orientés objet de toutes les versions de PHP 8 :

| Version | Sortie | Nouveautés objet principales |
|---------|--------|------------------------------|
| **8.0** | 2020 | Promotion de propriétés dans le constructeur, arguments nommés, `match`, opérateur nullsafe `?->`, types union, attributs |
| **8.1** | 2021 | Énumérations (`enum`), propriétés `readonly`, syntaxe de callable de première classe `f(...)`, `new` dans les initialiseurs, constantes `final` |
| **8.2** | 2022 | Classes `readonly`, constantes dans les traits, dépréciation des propriétés dynamiques, types DNF |
| **8.3** | 2023 | Constantes de classe typées, attribut `#[\Override]`, clonage profond des propriétés `readonly` |
| **8.4** | 2024 | **Property hooks**, **visibilité asymétrique**, `new SansParenthèses()->methode()`, objets paresseux (lazy objects) |
| **8.5** | 2025 | Opérateur pipe `\|>`, `final` sur les propriétés promues, closures dans les expressions constantes |

Ressources officielles et tutoriels complémentaires :

- [Manuel PHP — Classes et objets](https://www.php.net/manual/fr/language.oop5.php)
- [OpenClassrooms — Programmez en orienté objet en PHP](https://openclassrooms.com/fr/courses/1665806-programmez-en-oriente-objet-en-php)
- [Pierre Giraud — Introduction à la POO](https://www.pierre-giraud.com/php-mysql-apprendre-coder-cours/introduction-programmation-orientee-objet/)


### 🎮 Les exercices

Chaque chapitre a sa série d'exercices dans le dossier **[exercices/](./exercices/)** — Tamagotchi, tournoi de super-héros, machine à café, borne d'arcade et bien d'autres. Les plus emblématiques sont repris directement dans le cours, aux endroits marqués 🎮.

## Menu de navigation
- [code](./)
- [🎮 Exercices par chapitre](./exercices/)
- [Introduction](#php-8-orienté-objet)
    - [0. Du procédural à l'objet](#0-du-procédural-à-lobjet)
    - [1. Les classes et les objets](#1-les-classes-et-les-objets)
        - [1.1. Déclaration d'une classe](#11-déclaration-dune-classe)
        - [1.2. La visibilité des propriétés et des méthodes](#12-la-visibilité-des-propriétés-et-des-méthodes)
        - [1.3. Les propriétés typées](#13-les-propriétés-typées)
        - [1.4. Instanciation d'une classe](#14-instanciation-dune-classe)
        - [1.5. Accès aux propriétés et aux méthodes depuis l'intérieur de la classe](#15-accès-aux-propriétés-et-aux-méthodes-depuis-lintérieur-de-la-classe)
        - [1.6. Accès depuis l'extérieur de la classe et propriétés dynamiques](#16-accès-depuis-lextérieur-de-la-classe-et-propriétés-dynamiques)
        - [1.7. Les constructeurs](#17-les-constructeurs)
        - [1.8. La promotion de propriétés dans le constructeur (PHP 8.0)](#18-la-promotion-de-propriétés-dans-le-constructeur-php-80)
        - [1.9. Les arguments nommés (PHP 8.0)](#19-les-arguments-nommés-php-80)
        - [1.10. Les propriétés readonly (PHP 8.1) et les classes readonly (PHP 8.2)](#110-les-propriétés-readonly-php-81-et-les-classes-readonly-php-82)
        - [1.11. Les getters et les setters](#111-les-getters-et-les-setters)
        - [1.12. Les property hooks et la visibilité asymétrique (PHP 8.4)](#112-les-property-hooks-et-la-visibilité-asymétrique-php-84)
        - [1.13. Les méthodes magiques](#113-les-méthodes-magiques)
        - [1.14. L'opérateur nullsafe (PHP 8.0)](#114-lopérateur-nullsafe-php-80)
        - [1.15. Exercices ludiques — série 1](#115-exercices-ludiques--série-1)
    - [2. L'héritage](#2-lhéritage)
        - [2.1. Le mot clé extends](#21-le-mot-clé-extends)
        - [2.2. L'héritage multiple](#22-lhéritage-multiple)
        - [2.3. L'héritage et la visibilité](#23-lhéritage-et-la-visibilité)
        - [2.4. La redéfinition de méthodes et l'attribut #[\Override] (PHP 8.3)](#24-la-redéfinition-de-méthodes-et-lattribut-override-php-83)
        - [2.5. Les constantes de classe](#25-les-constantes-de-classe)
        - [2.6. Les classes abstraites](#26-les-classes-abstraites)
        - [2.7. Les méthodes et les propriétés statiques](#27-les-méthodes-et-les-propriétés-statiques)
        - [2.8. Les classes et méthodes finales](#28-les-classes-et-méthodes-finales)
        - [2.9. Les interfaces](#29-les-interfaces)
        - [2.10. Les traits](#210-les-traits)
        - [2.11. Exercices ludiques — série 2](#211-exercices-ludiques--série-2)
    - [3. Les énumérations (PHP 8.1)](#3-les-énumérations-php-81)
        - [3.1. Les enums purs](#31-les-enums-purs)
        - [3.2. Les enums adossés (backed enums)](#32-les-enums-adossés-backed-enums)
        - [3.3. Enums, méthodes, interfaces et match](#33-enums-méthodes-interfaces-et-match)
        - [3.4. Exercice ludique — Pierre, Feuille, Ciseaux](#34-exercice-ludique--pierre-feuille-ciseaux)
    - [4. Les espaces de noms](#4-les-espaces-de-noms)
    - [5. Auto-chargement des classes](#5-auto-chargement-des-classes)
    - [6. Les exceptions](#6-les-exceptions)
        - [6.1. Exercice ludique — La machine à café](#61-exercice-ludique--la-machine-à-café)
    - [7. Le match et les autres nouveautés PHP 8 utiles](#7-le-match-et-les-autres-nouveautés-php-8-utiles)
    - [8. Le mapping de tables SQL en classes PHP](#8-le-mapping-de-tables-sql-en-classes-php)
        - [8.1. Utilisation d'une classe abstraite pour l'hydratation](#81-utilisation-dune-classe-abstraite-pour-lhydratation)
    - [9. Les Managers](#9-les-managers)
        - [9.1. Exercice ludique — Le Gardien de Créatures](#91-exercice-ludique--le-gardien-de-créatures)
    - [10. Mini-projet final — L'Arène des Héros](#10-mini-projet-final--larène-des-héros)
    - [Annexe — Mémo des nouveautés objet PHP 8.0 à 8.5](#annexe--mémo-des-nouveautés-objet-php-80-à-85)

---


### 0. Du procédural à l'objet

Vous savez déjà écrire ceci en procédural :

```php
// Un utilisateur représenté par un tableau associatif
$user = [
    'name'  => 'Aline',
    'email' => 'aline@example.com',
];

// Une fonction qui travaille sur ce tableau
function afficherUser(array $user): void
{
    echo $user['name'] . ' (' . $user['email'] . ')';
}

afficherUser($user);
```

Cela fonctionne… mais rien n'empêche d'écrire `$user['emial']` (faute de frappe silencieuse), de mettre un entier dans `name` ou d'oublier une clé. Les **données** (le tableau) et les **comportements** (les fonctions) vivent chacun de leur côté.

La POO réunit les deux dans une même **brique** : la classe.

```php
class User
{
    public function __construct(
        private string $name,
        private string $email,
    ) {}

    public function afficher(): void
    {
        echo $this->name . ' (' . $this->email . ')';
    }
}

$user = new User('Aline', 'aline@example.com');
$user->afficher();
```

Avantages immédiats :

- une faute de frappe sur une propriété ou une méthode provoque une **erreur claire** ;
- les types (`string`, `int`…) sont **vérifiés** par PHP ;
- les données et les comportements qui vont ensemble sont **regroupés** ;
- on peut protéger les données contre des modifications invalides (encapsulation).

Le reste du cours détaille toutes les briques de cet exemple, une à une.

> 🎮 **Exercices du chapitre 0** : [la playlist du stage et la chasse aux bugs](./exercices/chapitre-00.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 1. Les classes et les objets

**Définition** : Une **classe** est une structure qui permet de définir des objets. Une classe est un modèle qui décrit les caractéristiques communes d'un groupe d'objets.

Un **objet** est une **instance d'une classe**. Un objet est une entité qui possède des propriétés, des constantes et des méthodes.

> 🍰 **Image à retenir** : la classe est le *moule à gaufres*, les objets sont les *gaufres*. Un seul moule, autant de gaufres que l'on veut — et chaque gaufre peut avoir sa propre garniture (ses propres valeurs de propriétés).

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.1. Déclaration d'une classe

La déclaration d'une classe en PHP commence par le mot clé `class`, suivi du nom de la classe et de son contenu entre accolades. Par convention ([PSR-1](https://www.php-fig.org/psr/psr-1/)), le nom d'une classe s'écrit en `PascalCase` (chaque mot commence par une majuscule).

```php
class MaClasse
{
    // Propriétés
    private string $proprietePrivee = '';
    public string $proprietePublique = 'Valeur par défaut';

    // Constantes
    public const string MA_CONSTANTE = 'Valeur constante';

    // Méthodes
    public function methodePublique(): void
    {
        echo "Ceci est une méthode publique";
    }

    private function methodePrivee(): void
    {
        echo "Ceci est une méthode privée";
    }
}
```

Dans cet exemple, la classe s'appelle `MaClasse`. Elle a deux propriétés : une propriété privée `$proprietePrivee` et une propriété publique `$proprietePublique` initialisée à `'Valeur par défaut'`.

Elle a également une constante `MA_CONSTANTE`. Une constante de classe est accessible depuis l'extérieur en utilisant le nom de la classe suivi de l'opérateur de résolution de portée `::` et du nom de la constante :

```php
echo MaClasse::MA_CONSTANTE;
```

> 💡 Depuis **PHP 8.3**, les constantes de classe peuvent être **typées** (`public const string MA_CONSTANTE = '...'`), nous y reviendrons au chapitre [2.5](#25-les-constantes-de-classe).

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.2. La visibilité des propriétés et des méthodes

Les propriétés, les constantes et les méthodes d'une classe peuvent avoir trois niveaux de visibilité : `public`, `protected` ou `private`.

- **public**

Les propriétés et les méthodes publiques sont accessibles depuis l'extérieur de la classe, ainsi que dans la classe elle-même et ses classes enfants.

- **protected**

Les propriétés et les méthodes protégées ne sont accessibles que dans la classe elle-même et ses classes enfants.

- **private**

Les propriétés et les méthodes privées ne sont accessibles que dans la classe elle-même.

> 🏰 **Image à retenir** : imaginez un château.
> - `public` : la cour d'entrée, tout le monde peut y aller ;
> - `protected` : les appartements royaux, réservés à la famille (la classe et ses enfants) ;
> - `private` : le journal intime du roi, personne d'autre que lui.

Ce mécanisme s'appelle l'**encapsulation** : on cache les détails internes d'un objet et on n'expose que ce qui est nécessaire. C'est l'un des grands principes de la POO — nous verrons son utilité concrète avec les getters et les setters.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.3. Les propriétés typées

Depuis PHP 7.4, les propriétés peuvent (et devraient !) être **typées** :

```php
class Personnage
{
    public string $nom;
    public int $pointsDeVie = 100;
    public ?string $surnom = null;      // string OU null (type nullable)
    public int|float $force = 10;       // type union (PHP 8.0)
    public array $inventaire = [];
}
```

Si l'on tente d'affecter une valeur du mauvais type, PHP lève une erreur `TypeError` :

```php
$p = new Personnage();
$p->pointsDeVie = "beaucoup"; // TypeError !
```

⚠️ **Attention** : une propriété typée **sans valeur par défaut** est dite *non initialisée*. La lire avant de lui avoir donné une valeur provoque une erreur :

```php
$p = new Personnage();
echo $p->nom; // Error : Typed property Personnage::$nom
              // must not be accessed before initialization
```

C'est pour cela qu'on initialise généralement les propriétés dans le **constructeur** (chapitre [1.7](#17-les-constructeurs)).

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.4. Instanciation d'une classe

Il est important de noter que la déclaration d'une classe en PHP ne crée pas directement un objet. Une classe est simplement une structure qui décrit les propriétés et les méthodes d'un objet. Pour **créer un objet** à partir d'une classe, vous devez **instancier** la classe en utilisant le mot clé `new` :

```php
$objet = new MaClasse();
```

Dans cet exemple, nous avons instancié la classe `MaClasse`. L'objet est stocké en mémoire et la variable `$objet` est une étiquette pointant vers celui-ci.

Conséquence importante : quand on copie la variable, on ne copie **pas** l'objet, seulement l'étiquette !

```php
$a = new MaClasse();
$b = $a;                       // $a et $b pointent vers LE MÊME objet
$b->proprietePublique = 'Modifiée';
echo $a->proprietePublique;    // 'Modifiée' aussi !

$c = clone $a;                 // clone crée une VRAIE copie indépendante
```

> 💡 **PHP 8.4** permet même d'enchaîner directement un appel de méthode sur une instanciation sans parenthèses supplémentaires :
> ```php
> // Avant PHP 8.4, il fallait écrire (new MaClasse())->methodePublique();
> new MaClasse()->methodePublique();
> ```

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.5. Accès aux propriétés et aux méthodes depuis l'intérieur de la classe

Lorsque vous êtes à l'intérieur d'une classe :

- `$this` désigne **l'objet courant** (l'instance en train d'exécuter le code) — on l'utilise avec l'opérateur flèche `->` ;
- `self` désigne **la classe elle-même** — on l'utilise avec l'opérateur de résolution de portée `::` pour les constantes et les membres statiques.

```php
class MaClasse
{
    // Propriétés
    private int $proprietePrivee = 0;
    public string $proprietePublique = 'Valeur par défaut';

    // Constantes
    public const string MA_CONSTANTE = 'Valeur constante';

    // Méthodes
    public function methodePublique(): string
    {
        return "Ceci est une méthode publique";
    }

    private function methodePrivee(): string
    {
        return "Ceci est une méthode privée";
    }

    public function methodePublique2(): string
    {
        // accès aux propriétés de l'objet courant via $this
        $this->proprietePrivee = 5;
        $this->proprietePublique = "Nouvelle valeur";
        // appel d'une méthode de l'objet courant
        $sortie = $this->methodePublique();
        // accès à une constante de la classe via self::
        $sortie .= " " . self::MA_CONSTANTE;
        return $sortie;
    }
}
```

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.6. Accès depuis l'extérieur de la classe et propriétés dynamiques

Lorsque vous êtes à l'extérieur d'une classe, vous pouvez accéder à ses propriétés et à ses méthodes **publiques** en utilisant l'opérateur flèche `->` :

```php
echo $objet->proprietePublique;
echo $objet->methodePublique();
```

**Attention**, une propriété publique peut être modifiée depuis l'extérieur de la classe, ce qui n'est pas le cas d'une propriété privée ou protégée :

```php
$objet->proprietePublique = 'Nouvelle valeur';
```

Historiquement, PHP permettait même de créer une propriété publique **à la volée**, sans qu'elle existe dans la classe :

```php
$objet->proprieteInventee = 'Valeur'; // "propriété dynamique"
```

**C'est une mauvaise pratique** (source de bugs silencieux) et, bonne nouvelle : depuis **PHP 8.2**, la création de propriétés dynamiques est **dépréciée** — PHP émet un avertissement `Deprecated`, et cela deviendra une erreur dans une version future.

Si (et seulement si) une classe a réellement besoin de propriétés dynamiques, elle doit le déclarer explicitement avec l'attribut `#[\AllowDynamicProperties]` :

```php
#[\AllowDynamicProperties]
class SacFourreTout
{
}

$sac = new SacFourreTout();
$sac->nImporteQuoi = 42; // autorisé, aucun avertissement
```

Dans ce cours, nous déclarerons **toujours** nos propriétés dans la classe.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.7. Les constructeurs

Le constructeur est une méthode spéciale (méthode magique) qui est appelée automatiquement lorsqu'un objet est instancié. Le constructeur est généralement utilisé pour initialiser les propriétés d'un objet.

Le constructeur d'une classe est défini en créant une méthode avec le nom `__construct()` :

```php
class MaClasse
{
    public function __construct()
    {
        // Code du constructeur
    }
}
```

C'est au constructeur que l'on passe les paramètres lors de l'instanciation de la classe :

```php
$objet = new MaClasse($param1, $param2);
```

Voici un exemple de constructeur « classique » :

```php
class Personnage
{
    private string $nom;
    private int $pointsDeVie;

    public function __construct(string $nom, int $pointsDeVie)
    {
        $this->nom = $nom;
        $this->pointsDeVie = $pointsDeVie;
    }
}

$hero = new Personnage('Aria', 100);
```

Notez la répétition : `$nom` apparaît **trois fois** (déclaration, paramètre, affectation). PHP 8.0 a justement introduit une syntaxe pour éliminer cette répétition : la **promotion de propriétés**, que nous voyons tout de suite.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.8. La promotion de propriétés dans le constructeur (PHP 8.0)

**PHP 8.0** introduit la **promotion de propriétés du constructeur** (*constructor property promotion*) : en ajoutant une visibilité (`public`, `protected` ou `private`) devant un paramètre du constructeur, PHP **déclare la propriété et l'affecte automatiquement**.

Le code du chapitre précédent devient :

```php
class Personnage
{
    public function __construct(
        private string $nom,
        private int $pointsDeVie = 100,
    ) {}
}

$hero = new Personnage('Aria');
$boss = new Personnage('Malakar', 500);
```

C'est strictement équivalent à la version longue : les propriétés `$nom` et `$pointsDeVie` existent, sont privées, et sont initialisées avec les valeurs reçues. On peut bien sûr y accéder avec `$this->nom` dans les méthodes de la classe.

On peut mélanger paramètres promus et paramètres classiques, et ajouter du code dans le corps du constructeur :

```php
class Personnage
{
    private string $cri;

    public function __construct(
        private string $nom,
        private int $pointsDeVie = 100,
        string $humeur = 'joyeuse',
    ) {
        // $humeur est un simple paramètre : PAS une propriété
        $this->cri = $humeur === 'joyeuse' ? 'Youpi !' : 'Grmbl…';
    }
}
```

> 💡 Depuis **PHP 8.5**, une propriété promue peut aussi être déclarée `final` : `public final string $nom`.

Cette syntaxe est aujourd'hui **la manière standard** d'écrire des constructeurs en PHP moderne — vous la retrouverez partout (Symfony, Laravel…).

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.9. Les arguments nommés (PHP 8.0)

**PHP 8.0** permet de passer les arguments **par leur nom** plutôt que par leur position. C'est très pratique pour les constructeurs qui ont beaucoup de paramètres optionnels :

```php
class Pizza
{
    public function __construct(
        private string $pate = 'classique',
        private string $sauce = 'tomate',
        private bool $supplementFromage = false,
        private bool $ananas = false, // 😱
    ) {}
}

// Sans arguments nommés : il faut respecter l'ordre et tout préciser
$p1 = new Pizza('classique', 'tomate', true);

// Avec arguments nommés : on ne précise QUE ce qui change, dans l'ordre que l'on veut
$p2 = new Pizza(supplementFromage: true);
$p3 = new Pizza(ananas: true, pate: 'fine'); // la fameuse…
```

Les arguments nommés fonctionnent avec **toutes** les fonctions et méthodes, pas seulement les constructeurs.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.10. Les propriétés readonly (PHP 8.1) et les classes readonly (PHP 8.2)

**PHP 8.1** introduit le modificateur `readonly`, qui permet de déclarer une propriété en **lecture seule**.

Règles du jeu :

- la propriété doit être **typée** ;
- elle ne peut pas avoir de valeur par défaut dans sa déclaration ;
- elle ne peut être **initialisée qu'une seule fois**, et uniquement **depuis l'intérieur de la classe** (en pratique : dans le constructeur) ;
- une fois initialisée, elle ne peut **plus jamais être modifiée** — ni de l'extérieur, ni de l'intérieur, ni via un setter.

```php
class CarteIdentite
{
    public readonly string $numero;

    public function __construct(string $numero)
    {
        $this->numero = $numero; // initialisation : OK (une seule fois)
    }
}

$carte = new CarteIdentite('BE-2026-0042');

// Lecture : OK, la propriété est publique
echo $carte->numero;

// Écriture : IMPOSSIBLE, même depuis un setter
$carte->numero = 'FAUX-PAPIERS';
// Error: Cannot modify readonly property CarteIdentite::$numero
```

C'est l'outil parfait pour les données **immuables** : un identifiant, une date de création, des coordonnées… On obtient la commodité d'une propriété publique (pas besoin de getter) avec la sécurité d'une propriété privée (pas de modification possible).

Le `readonly` se combine très bien avec la promotion de propriétés :

```php
class Point
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
    ) {}
}

$p = new Point(2.5, 4.0);
echo $p->x; // 2.5
```

**PHP 8.2** va plus loin avec les **classes readonly** : toutes les propriétés de la classe deviennent automatiquement `readonly` :

```php
readonly class Point
{
    public function __construct(
        public float $x,
        public float $y,
    ) {}
}
```

> 💡 Et le clonage ? Un objet immuable ne se modifie pas… mais on peut créer une **copie modifiée** (patron « wither ») :
> ```php
> readonly class Point
> {
>     public function __construct(public float $x, public float $y) {}
>
>     public function avecX(float $x): static
>     {
>         return new static($x, $this->y);
>     }
> }
>
> $a = new Point(1.0, 2.0);
> $b = $a->avecX(5.0); // nouveau Point(5.0, 2.0), $a est intact
> ```
> Depuis **PHP 8.3**, la méthode magique `__clone()` a même le droit de réinitialiser des propriétés `readonly` pendant un `clone`.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.11. Les getters et les setters

Les getters et les setters sont utilisés pour accéder aux propriétés d'une classe.

Un `getter` est une méthode qui permet de **lire** une propriété privée ou protégée.

Un `setter` est une méthode qui permet de **modifier** une propriété privée ou protégée — et surtout de **contrôler** la valeur avant de l'accepter !

Les bonnes pratiques de programmation orientée objet recommandent de rendre les propriétés privées (ou protégées) et d'utiliser des getters et des setters pour y accéder. C'est le principe d'**encapsulation** : l'objet reste maître de ses données.

```php
class Personnage
{
    public function __construct(
        private string $nom,
        private int $pointsDeVie = 100,
    ) {
        // on passe par le setter pour profiter de sa validation
        $this->setPointsDeVie($pointsDeVie);
    }

    // Getters : lecture
    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPointsDeVie(): int
    {
        return $this->pointsDeVie;
    }

    // Setters : écriture contrôlée
    public function setNom(string $nom): void
    {
        $this->nom = trim($nom);
    }

    public function setPointsDeVie(int $pointsDeVie): void
    {
        // le setter est le garde du corps de la propriété :
        // impossible d'avoir des PV négatifs ou > 100
        $this->pointsDeVie = max(0, min(100, $pointsDeVie));
    }
}
```

Utilisation depuis l'extérieur de la classe :

```php
$hero = new Personnage('Aria');

// Lecture via les getters
echo $hero->getNom();          // Aria
echo $hero->getPointsDeVie();  // 100

// Écriture via les setters
$hero->setPointsDeVie(-9999);  // tentative de triche…
echo $hero->getPointsDeVie();  // 0 → le setter a fait son travail !
```

Sans encapsulation (propriété publique), n'importe quel code pourrait écrire `$hero->pointsDeVie = -9999;` et casser la logique du jeu. Avec le setter, la règle « les PV restent entre 0 et 100 » est garantie **en un seul endroit**.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.12. Les property hooks et la visibilité asymétrique (PHP 8.4)

**PHP 8.4** apporte deux nouveautés majeures qui simplifient énormément l'écriture des getters/setters.

##### Les property hooks

Un **property hook** permet d'attacher directement à une propriété un comportement de lecture (`get`) et/ou d'écriture (`set`). De l'extérieur, on utilise la propriété normalement — mais le hook s'exécute à chaque accès :

```php
class Personnage
{
    // hook "set" : la validation du setter, sans setter !
    public int $pointsDeVie = 100 {
        set(int $valeur) {
            $this->pointsDeVie = max(0, min(100, $valeur));
        }
    }

    public function __construct(public string $nom) {}
}

$hero = new Personnage('Aria');
$hero->pointsDeVie = -9999;   // le hook set intercepte l'écriture
echo $hero->pointsDeVie;      // 0
```

Un hook `get` permet aussi de créer des **propriétés virtuelles**, calculées à la volée sans être stockées :

```php
class Personnage
{
    public function __construct(
        public string $prenom,
        public string $nom,
    ) {}

    // propriété virtuelle : calculée, jamais stockée
    public string $nomComplet {
        get => $this->prenom . ' ' . $this->nom;
    }
}

$hero = new Personnage('Aria', 'Ventacier');
echo $hero->nomComplet; // Aria Ventacier
```

##### La visibilité asymétrique

Toujours en **PHP 8.4**, on peut donner une visibilité **différente en lecture et en écriture** avec `private(set)` ou `protected(set)` :

```php
class Compteur
{
    // lisible par tout le monde, modifiable UNIQUEMENT dans la classe
    public private(set) int $valeur = 0;

    public function incrementer(): void
    {
        $this->valeur++; // OK : on est dans la classe
    }
}

$c = new Compteur();
$c->incrementer();
echo $c->valeur;  // 1 → lecture publique, pas besoin de getter
$c->valeur = 42;  // Error : écriture réservée à la classe
```

> 🎯 **En résumé** : entre `readonly` (8.1), les property hooks et la visibilité asymétrique (8.4), le PHP moderne a de moins en moins besoin des getters/setters « à l'ancienne ». Il reste toutefois indispensable de connaître les getters/setters classiques : l'immense majorité du code existant les utilise, et nous les emploierons dans le chapitre sur l'hydratation.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.13. Les méthodes magiques

Les méthodes magiques sont des méthodes spéciales, reconnaissables à leur préfixe `__`, que PHP appelle automatiquement dans certaines situations. Vous connaissez déjà `__construct()`. Voici les plus utiles :

| Méthode | Appelée quand… |
|---------|----------------|
| `__construct()` | l'objet est créé avec `new` |
| `__destruct()` | l'objet est détruit (fin de script, `unset`) |
| `__toString()` | l'objet est utilisé comme une chaîne (`echo $objet`) |
| `__get($nom)` / `__set($nom, $valeur)` | lecture/écriture d'une propriété inaccessible |
| `__call($nom, $args)` | appel d'une méthode inaccessible |
| `__clone()` | l'objet est copié avec `clone` |
| `__invoke()` | l'objet est appelé comme une fonction : `$objet()` |

Exemple avec `__toString()`, la plus utilisée :

```php
class Personnage
{
    public function __construct(
        private string $nom,
        private int $pointsDeVie = 100,
    ) {}

    public function __toString(): string
    {
        return sprintf('%s (%d PV)', $this->nom, $this->pointsDeVie);
    }
}

$hero = new Personnage('Aria');
echo $hero; // Aria (100 PV)
```

> 💡 Depuis PHP 8.0, une classe qui définit `__toString()` implémente automatiquement l'interface `Stringable`.

La liste complète se trouve dans le manuel : https://www.php.net/manual/fr/language.oop5.magic.php — nous les découvrirons au fur et à mesure de nos besoins.

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.14. L'opérateur nullsafe (PHP 8.0)

**PHP 8.0** introduit l'opérateur **nullsafe** `?->`. Il permet d'appeler une méthode ou de lire une propriété sur une valeur qui peut être `null`, sans provoquer d'erreur : si la valeur est `null`, toute la chaîne renvoie simplement `null`.

```php
class Adresse
{
    public function __construct(public string $ville) {}
}

class User
{
    public function __construct(
        public string $nom,
        public ?Adresse $adresse = null,
    ) {}
}

$aline = new User('Aline', new Adresse('Charleroi'));
$bob   = new User('Bob'); // pas d'adresse

// Avant PHP 8 : vérifications en cascade
$ville = $bob->adresse !== null ? $bob->adresse->ville : null;

// Avec le nullsafe :
echo $aline->adresse?->ville; // Charleroi
echo $bob->adresse?->ville;   // null (aucune erreur !)
```

---

[Menu de navigation](#menu-de-navigation)

---


#### 1.15. Exercices ludiques — série 1

##### 🐣 Exercice 1.A — Le Tamagotchi

Créez une classe `Tamagotchi` :

- propriétés **privées** : `$nom` (string), `$faim` (int, 50 au départ), `$energie` (int, 50 au départ), `$humeur` (int, 50 au départ) — toutes entre 0 et 100 ;
- utilisez la **promotion de propriétés** dans le constructeur pour `$nom` ;
- méthodes :
    - `manger()` : faim −30, énergie +5 ;
    - `dormir()` : énergie +40, faim +10 ;
    - `jouer()` : humeur +25, énergie −20, faim +15 ;
    - `parler()` : affiche un message différent selon son état (utilisez vos `if`… ou un `match`, voir chapitre 7 !) ;
- les valeurs doivent **toujours** rester entre 0 et 100 (setters ou property hooks à la rescousse) ;
- ajoutez `__toString()` pour afficher un joli bulletin de santé, par exemple :

```text
🐣 Pixel — faim: 20/100 | énergie: 35/100 | humeur: 75/100
```

Testez en faisant vivre une journée complète à votre créature. Bonus : si la faim atteint 100 ou l'énergie 0… affichez `💀 Pixel s'est enfui pour trouver une meilleure famille !`

##### 🦸 Exercice 1.B — La fabrique de super-héros

Créez une classe `SuperHeros` avec le constructeur suivant (promotion + valeurs par défaut) :

```php
public function __construct(
    private string $nom,
    private string $pouvoir = 'aucun',
    private string $couleurCape = 'rouge',
    private bool $porteSlipParDessus = false,
) {}
```

1. Instanciez au moins 3 héros en utilisant les **arguments nommés** pour ne préciser que ce qui change.
2. Ajoutez une méthode `sePresenter()` qui affiche par exemple :
   `Je suis Capitaine Gaufre, mon pouvoir est de lancer du sucre impalpable, et OUI je porte mon slip par-dessus mon costume !`
3. Ajoutez une propriété **readonly** `public readonly string $identiteSecrete` initialisée dans le constructeur, et vérifiez qu'il est impossible de la modifier (essayez, lisez l'erreur, comprenez-la !).

##### 🔐 Exercice 1.C — Le coffre-fort

Créez une classe `CoffreFort` :

- propriété privée `$code` (string, définie dans le constructeur) ;
- propriété privée `$contenu` (array, vide au départ) ;
- propriété privée `$verrouille` (bool, `true` au départ) ;
- `deverrouiller(string $tentative): bool` — compare avec le code, 3 échecs consécutifs = coffre bloqué à jamais ;
- `deposer(string $objet): void` et `ouvrir(): array` — ne fonctionnent que si le coffre est déverrouillé.

Testez le scénario : un pirate essaie 3 mauvais codes, puis le bon… trop tard ! 🏴‍☠️

> 🎮 **La suite du chapitre 1** : [le dé parlant, la jauge magique (property hooks) et le quiz « que va afficher ce code ? »](./exercices/chapitre-01.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 2. L'héritage

L'héritage est un concept de programmation orientée objet qui permet à une classe d'hériter des propriétés et des méthodes d'une autre classe.

Une classe qui hérite d'une autre classe est appelée **classe enfant** ou **classe dérivée**. La classe dont la classe enfant hérite est appelée **classe parente** ou **classe de base**.

> 🧬 **Image à retenir** : `Chien` **est un** `Animal`. Si la phrase « X est un Y » a du sens, l'héritage est probablement le bon outil. Si la phrase est plutôt « X possède un Y » (une voiture *possède* un moteur), on utilise la **composition** (une propriété contenant un objet), pas l'héritage.

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.1. Le mot clé extends

Pour hériter d'une classe, utilisez le mot clé `extends` :

```php
class Animal
{
    public function __construct(
        protected string $nom,
    ) {}

    public function manger(): string
    {
        return "{$this->nom} mange.";
    }
}

class Chien extends Animal
{
    // Chien hérite de $nom et de manger()…
    // …et ajoute son propre comportement :
    public function aboyer(): string
    {
        return "{$this->nom} : Wouf !";
    }
}

$rex = new Chien('Rex');
echo $rex->manger(); // Rex mange.  (méthode héritée)
echo $rex->aboyer(); // Rex : Wouf ! (méthode propre)
```

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.2. L'héritage multiple

PHP ne supporte pas l'héritage multiple, c'est-à-dire qu'une classe ne peut hériter que d'une seule classe parente. Cependant, une classe peut **implémenter plusieurs interfaces** (chapitre [2.9](#29-les-interfaces)) et **utiliser plusieurs traits** (chapitre [2.10](#210-les-traits)).

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.3. L'héritage et la visibilité

Lorsqu'une classe enfant hérite d'une classe parente :

- les membres **publics** et **protégés** du parent sont accessibles dans la classe enfant ;
- les membres **privés** du parent existent bien dans l'objet, mais ne sont **pas accessibles** depuis le code de la classe enfant — seul le code de la classe parente peut y accéder (via ses getters/setters ou méthodes protégées, par exemple).

```php
class Animal
{
    private string $secret = 'je fais semblant de dormir';
    protected string $nom = 'Anonyme';
}

class Chien extends Animal
{
    public function test(): void
    {
        echo $this->nom;    // OK : protected est accessible dans l'enfant
        echo $this->secret; // Error : private n'est PAS accessible ici
    }
}
```

Lors de la redéfinition d'un membre hérité, la classe enfant peut **élargir** la visibilité (par exemple `protected` → `public`), mais jamais la **réduire** (`public` → `protected` est interdit).

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.4. La redéfinition de méthodes et l'attribut #[\Override] (PHP 8.3)

Une classe enfant peut **redéfinir** (*override*) une méthode héritée : elle fournit sa propre version, qui remplace celle du parent.

> 📖 **Vocabulaire** : en français, on parle souvent de « surcharge » pour ce mécanisme, mais le terme exact est **redéfinition** (*overriding*). La *surcharge* (*overloading* — plusieurs méthodes de même nom avec des signatures différentes) n'existe pas en PHP.

La méthode redéfinie doit avoir le même nom et une signature **compatible** avec celle du parent (même nombre de paramètres obligatoires, visibilité identique ou plus large).

```php
class Animal
{
    public function crier(): string
    {
        return "...";
    }
}

class Chat extends Animal
{
    public function crier(): string
    {
        return "Miaou !";
    }
}
```

Pour appeler la version de la classe parente depuis la classe enfant, utilisez le mot clé `parent` :

```php
class ChatDramatique extends Chat
{
    public function crier(): string
    {
        // on récupère le cri du parent et on l'enrichit
        return strtoupper(parent::crier()) . " MIAOU ! MIAAAAOUUU !";
    }
}
```

##### L'attribut #[\Override] (PHP 8.3)

Depuis **PHP 8.3**, on peut marquer une méthode avec l'attribut `#[\Override]` pour déclarer explicitement : « cette méthode redéfinit une méthode du parent ». Si ce n'est pas le cas (faute de frappe, méthode renommée dans le parent…), PHP lève une erreur — au lieu de créer silencieusement une nouvelle méthode qui ne redéfinit rien !

```php
class Chat extends Animal
{
    #[\Override]
    public function crier(): string   // OK : Animal::crier() existe
    {
        return "Miaou !";
    }

    #[\Override]
    public function criez(): string   // Fatal error : rien à redéfinir !
    {                                 // (la faute de frappe est détectée)
        return "Miaou ?";
    }
}
```

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.5. Les constantes de classe

Il est possible de définir des constantes de classe, qui restent identiques et non modifiables. La visibilité par défaut des constantes de classe est `public`.

À partir de **PHP 7.1.0**, les modificateurs de visibilité sont autorisés sur les constantes de classe.

Les constantes de classe peuvent être redéfinies par une classe enfant. À partir de **PHP 8.1.0**, une constante déclarée `final` ne peut pas être redéfinie par une classe enfant.

```php
class MaClasse
{
    const MA_CONSTANTE = 'Valeur constante'; // publique par défaut
    public const MA_CONSTANTE_PUBLIQUE = 'Valeur constante publique';
    protected const MA_CONSTANTE_PROTEGEE = 'Valeur constante protégée';
    private const MA_CONSTANTE_PRIVEE = 'Valeur constante privée';
    // ne peut pas être redéfinie par une classe enfant (PHP 8.1)
    final const MA_CONSTANTE_FINALE = 'Valeur constante finale';
}

// fonctionnent
echo MaClasse::MA_CONSTANTE;
echo MaClasse::MA_CONSTANTE_PUBLIQUE;
echo MaClasse::MA_CONSTANTE_FINALE;
// génèrent une erreur (visibilité insuffisante depuis l'extérieur)
echo MaClasse::MA_CONSTANTE_PROTEGEE;
echo MaClasse::MA_CONSTANTE_PRIVEE;
```

À partir de **PHP 8.3**, les constantes de classe peuvent être **typées** :

```php
class MaClasse
{
    public const string|float MA_CONSTANTE = 'Valeur constante';
}

class MaClasseEnfant extends MaClasse
{
    // lors de la redéfinition, le type déclaré doit être identique
    public const string|float MA_CONSTANTE = 25.36;
}

echo MaClasse::MA_CONSTANTE;        // Valeur constante
echo MaClasseEnfant::MA_CONSTANTE;  // 25.36
```

> 💡 **PHP 8.3** permet aussi d'accéder dynamiquement à une constante : `MaClasse::{$nomDeConstante}`.

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.6. Les classes abstraites

Une classe abstraite est une classe qui **ne peut pas être instanciée**. Elle sert de modèle incomplet dont les classes enfants devront combler les trous.

Pour définir une classe abstraite, utilisez le mot clé `abstract` :

```php
abstract class MaClasseAbstraite
{
    // Code de la classe abstraite
}

$objet = new MaClasseAbstraite(); // Error : cannot instantiate abstract class
```

Une classe abstraite peut contenir des méthodes abstraites et des méthodes non abstraites.

Une **méthode abstraite** est une méthode sans corps : elle déclare « mes enfants DOIVENT fournir cette méthode », sans dire comment. Elle est définie avec le mot clé `abstract` et ne peut pas être `private` ni `final`.

```php
abstract class Animal
{
    public function __construct(protected string $nom) {}

    // Chaque animal crie à SA façon : impossible de l'écrire ici
    abstract public function crier(): string;

    // Par contre, tous les animaux se présentent pareil :
    public function sePresenter(): string
    {
        // une méthode concrète PEUT appeler une méthode abstraite :
        // au moment de l'exécution, ce sera la version de l'enfant
        return "Je suis {$this->nom} et je fais : " . $this->crier();
    }
}

class Vache extends Animal
{
    #[\Override]
    public function crier(): string
    {
        return "Meuh !";
    }
}

$marguerite = new Vache('Marguerite');
echo $marguerite->sePresenter(); // Je suis Marguerite et je fais : Meuh !
```

Une classe enfant **doit** définir toutes les méthodes abstraites de son parent (sinon elle doit être elle-même déclarée abstraite).

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.7. Les méthodes et les propriétés statiques

Une méthode ou une propriété **statique** appartient à la **classe** elle-même, et non à ses instances. On peut l'utiliser sans instancier la classe.

Pour définir une méthode ou une propriété statique, utilisez le mot clé `static` :

```php
class Compteur
{
    // Propriété statique : PARTAGÉE par toutes les instances
    public static int $nbInstances = 0;

    public function __construct()
    {
        // self:: pour accéder aux membres statiques depuis l'intérieur
        self::$nbInstances++;
    }

    // Méthode statique
    public static function afficherCompte(): void
    {
        echo "Il existe " . self::$nbInstances . " objets Compteur.";
        // ⚠️ pas de $this dans une méthode statique :
        // il n'y a pas d'objet courant !
    }
}
```

Pour accéder à un membre statique depuis l'extérieur, utilisez le nom de la classe suivi de l'opérateur `::` :

```php
new Compteur();
new Compteur();

echo Compteur::$nbInstances;  // 2
Compteur::afficherCompte();   // Il existe 2 objets Compteur.
```

Usage classique : les **fabriques** (*factory methods*), des méthodes statiques qui créent des objets :

```php
class De
{
    private function __construct(public readonly int $faces) {}

    public static function classique(): static
    {
        return new static(6);
    }

    public static function deDonjon(): static
    {
        return new static(20);
    }

    public function lancer(): int
    {
        return random_int(1, $this->faces);
    }
}

$d20 = De::deDonjon();
echo $d20->lancer(); // entre 1 et 20 — jet d'initiative !
```

> 💡 Le type de retour `static` (PHP 8.0) signifie « une instance de la classe sur laquelle la méthode a été appelée » — pratique avec l'héritage.

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.8. Les classes et méthodes finales

Une classe **finale** est une classe qui ne peut pas être héritée. Elle est utilisée pour empêcher la création de classes enfants.

```php
final class MaClasseFinale
{
    // Code de la classe finale
}

class Tentative extends MaClasseFinale {} // Fatal error !
```

Une méthode **finale** est une méthode qui ne peut pas être redéfinie par une classe enfant :

```php
class MaClasse
{
    final public function methodeFinale(): void
    {
        // Code de la méthode finale
    }
}
```

Rappel des autres usages de `final` déjà croisés : constantes `final` (PHP 8.1, chapitre [2.5](#25-les-constantes-de-classe)) et propriétés promues `final` (PHP 8.5, chapitre [1.8](#18-la-promotion-de-propriétés-dans-le-constructeur-php-80)).

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.9. Les interfaces

Une **interface** est un **contrat** : elle liste des méthodes (signatures uniquement, sans corps) que toute classe signataire s'engage à fournir. Contrairement à une classe abstraite, une interface ne contient aucune propriété d'instance ni code d'implémentation — uniquement des signatures de méthodes publiques (et éventuellement des constantes).

> 🔌 **Image à retenir** : une prise électrique. Peu importe la marque de l'appareil (la classe), s'il a la bonne fiche (implémente l'interface), on peut le brancher.

Pour définir une interface, utilisez le mot clé `interface` :

```php
interface Combattant
{
    public function attaquer(Combattant $cible): void;
    public function recevoirDegats(int $degats): void;
    public function estVivant(): bool;
}
```

Une classe s'engage à respecter le contrat avec le mot clé `implements`, et **doit** définir toutes les méthodes de l'interface :

```php
class Chevalier implements Combattant
{
    public function __construct(
        private string $nom,
        private int $pv = 100,
    ) {}

    public function attaquer(Combattant $cible): void
    {
        $cible->recevoirDegats(random_int(5, 15));
    }

    public function recevoirDegats(int $degats): void
    {
        $this->pv = max(0, $this->pv - $degats);
    }

    public function estVivant(): bool
    {
        return $this->pv > 0;
    }
}
```

L'intérêt : du code qui accepte **n'importe quel** combattant, sans connaître sa classe concrète :

```php
function duel(Combattant $a, Combattant $b): void
{
    while ($a->estVivant() && $b->estVivant()) {
        $a->attaquer($b);
        if ($b->estVivant()) {
            $b->attaquer($a);
        }
    }
}
// duel(new Chevalier('Aria'), new Dragon('Smaug')); → ça marche,
// du moment que Dragon implémente aussi Combattant !
```

C'est ce qu'on appelle le **polymorphisme** : un même code (`duel()`) fonctionne avec des objets de classes différentes.

Une classe peut implémenter plusieurs interfaces, séparées par des virgules :

```php
class MaClasse implements MaInterface1, MaInterface2
{
    // doit définir les méthodes des DEUX interfaces
}
```

Une interface peut hériter d'une ou plusieurs interfaces :

```php
interface MaInterfaceEnfant extends MaInterfaceParent1, MaInterfaceParent2
{
    // Code de l'interface enfant
}
```

Enfin, l'opérateur `instanceof` permet de vérifier qu'un objet implémente une interface (ou hérite d'une classe) :

```php
if ($objet instanceof Combattant) {
    // on peut l'envoyer dans l'arène !
}
```

> 💡 Depuis **PHP 8.4**, une interface peut aussi déclarer des propriétés avec hooks (`public string $nom { get; }`), le contrat portant alors sur l'accès à la propriété.

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.10. Les traits

Un **trait** est un ensemble de méthodes (et de propriétés) que l'on peut « copier-coller intelligemment » dans plusieurs classes. C'est la réponse de PHP à l'absence d'héritage multiple : une classe n'a qu'un seul parent, mais peut utiliser autant de traits qu'elle veut.

Pour définir un trait, utilisez le mot clé `trait` ; pour l'utiliser dans une classe, le mot clé `use` :

```php
trait Horodatable
{
    private ?DateTimeImmutable $creeLe = null;

    public function marquerCreation(): void
    {
        $this->creeLe = new DateTimeImmutable();
    }

    public function getCreeLe(): ?DateTimeImmutable
    {
        return $this->creeLe;
    }
}

class Article
{
    use Horodatable;
}

class Commentaire
{
    use Horodatable;
}

$article = new Article();
$article->marquerCreation();
// Article et Commentaire partagent le même code sans lien d'héritage !
```

Un trait peut contenir des méthodes abstraites (que la classe utilisatrice devra définir), des méthodes statiques et, depuis **PHP 8.2**, des **constantes** :

```php
trait Volant
{
    public const int ALTITUDE_MAX = 10000; // PHP 8.2 : constantes dans les traits

    abstract public function getNom(): string;

    public function voler(): string
    {
        return $this->getNom() . " s'envole ! 🕊️";
    }
}
```

Une classe peut utiliser plusieurs traits, et un trait peut lui-même utiliser d'autres traits :

```php
trait MonTrait1 { /* ... */ }
trait MonTrait2 { /* ... */ }

trait MonTrait3
{
    use MonTrait1, MonTrait2;
}

class MaClasse
{
    use MonTrait3; // récupère les trois !
}
```

> ⚠️ Si deux traits fournissent une méthode du même nom, PHP exige de résoudre le conflit avec `insteadof` / `as` — voir le [manuel](https://www.php.net/manual/fr/language.oop5.traits.php).

---

[Menu de navigation](#menu-de-navigation)

---


#### 2.11. Exercices ludiques — série 2

##### 🦁 Exercice 2.A — La ménagerie qui parle

1. Créez une classe **abstraite** `Animal` : propriété protégée `$nom`, méthode abstraite `crier(): string`, méthode concrète `sePresenter(): string` qui utilise `crier()`.
2. Créez `Chien`, `Chat` et `Perroquet` qui héritent d'`Animal`. Le perroquet a une particularité : son constructeur reçoit une phrase, et `crier()` la répète deux fois avec « Croâ ! » au milieu.
3. Marquez toutes vos redéfinitions avec `#[\Override]`.
4. Rangez tous vos animaux dans un tableau et faites-les se présenter dans une boucle : c'est le **polymorphisme** en action !

```text
Je suis Rex et je fais : Wouf !
Je suis Félix et je fais : Miaou !
Je suis Coco et je fais : À l'abordage ! Croâ ! À l'abordage !
```

##### ⚔️ Exercice 2.B — Le tournoi

1. Créez l'interface `Combattant` du chapitre [2.9](#29-les-interfaces).
2. Implémentez trois classes très différentes : `Chevalier` (attaques régulières), `Magicien` (attaques puissantes mais 1 fois sur 2 le sort échoue — `random_int(0, 1)`), `Poule` (1 dégât par attaque, mais 500 PV : l'usure !).
3. Écrivez la fonction `duel(Combattant $a, Combattant $b): Combattant` qui fait combattre tour par tour, affiche le déroulé, et renvoie le vainqueur.
4. Organisez un mini-tournoi à 4 participants (demi-finales + finale). Qui gagne le plus souvent ? La poule surprend souvent ! 🐔🏆

##### 🦸 Exercice 2.C — Les super-pouvoirs en kit

1. Créez les traits `Vole` (méthode `voler()`), `CracheDuFeu` (méthode `cracherDuFeu()`) et `Invisible` (méthode `disparaitre()`), chacun affichant un message rigolo.
2. Créez `Dragon` (vole + crache du feu), `Fantome` (vole + invisible) et `Politicien` (invisible uniquement, surtout après les élections).
3. Question à méditer et à justifier en commentaire : pourquoi des traits plutôt qu'un héritage `Dragon extends Volant` ?

##### 🧮 Exercice 2.D — Le compteur de population (statique)

Reprenez votre `Tamagotchi` (exercice 1.A) et ajoutez :

- une propriété **statique** `$population` incrémentée à chaque naissance (constructeur) ;
- une méthode statique `recensement(): string` qui affiche `🌍 Population mondiale de Tamagotchis : 42` ;
- une constante de classe typée `final const int SEUIL_SURPOPULATION = 10;` — au-delà, `recensement()` ajoute un avertissement dramatique.

> 🎮 **La suite du chapitre 2** : [le contrôle à l'entrée du zoo, la dynastie royale et le quiz « compilera, compilera pas ? »](./exercices/chapitre-02.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 3. Les énumérations (PHP 8.1)

Les **énumérations** (`enum`), introduites en **PHP 8.1**, permettent de définir un type qui n'a qu'un nombre **fini et fixé** de valeurs possibles. Fini les constantes de classe éparpillées ou les chaînes magiques `'brouillon'`, `'publié'`… qu'une faute de frappe suffit à casser !

---

#### 3.1. Les enums purs

```php
enum Statut
{
    case Brouillon;
    case Publie;
    case Archive;
}
```

Chaque `case` est une instance unique de l'enum. On l'utilise comme une constante de classe :

```php
$statut = Statut::Publie;

var_dump($statut === Statut::Publie); // true — comparaison stricte fiable
echo $statut->name;                   // "Publie" (le nom du case)

// Le grand intérêt : le typage !
function afficherBadge(Statut $statut): void
{
    // impossible de recevoir 'publiee', 'PUBLIE' ou 42 :
    // seule une des 3 valeurs de l'enum peut entrer ici
}

// Lister tous les cas :
foreach (Statut::cases() as $cas) {
    echo $cas->name . PHP_EOL;
}
```

---

#### 3.2. Les enums adossés (backed enums)

Un enum **adossé** (*backed enum*) associe à chaque cas une valeur scalaire (`string` ou `int`) — idéal pour stocker en base de données :

```php
enum Statut: string
{
    case Brouillon = 'brouillon';
    case Publie = 'publie';
    case Archive = 'archive';
}

// De l'enum vers la valeur (→ pour l'INSERT SQL) :
echo Statut::Publie->value; // "publie"

// De la valeur vers l'enum (→ après un SELECT SQL) :
$statut = Statut::from('publie');        // Statut::Publie
$statut = Statut::from('pubieee');       // ValueError : valeur inconnue !
$statut = Statut::tryFrom('pubieee');    // null (version tolérante)
```

---

#### 3.3. Enums, méthodes, interfaces et match

Un enum peut avoir des **méthodes**, des **constantes**, implémenter des **interfaces** et utiliser des **traits** (mais pas de propriétés d'instance ni d'héritage de classe). L'association enum + `match` (chapitre [7](#7-le-match-et-les-autres-nouveautés-php-8-utiles)) est particulièrement élégante :

```php
enum Statut: string
{
    case Brouillon = 'brouillon';
    case Publie = 'publie';
    case Archive = 'archive';

    public function libelle(): string
    {
        return match($this) {
            Statut::Brouillon => '📝 En cours de rédaction',
            Statut::Publie    => '✅ En ligne',
            Statut::Archive   => '📦 Archivé',
        };
    }

    public function estVisible(): bool
    {
        return $this === Statut::Publie;
    }
}

echo Statut::Brouillon->libelle(); // 📝 En cours de rédaction
```

> 💡 Bonus : si le `match($this)` oublie un `case` de l'enum et qu'il se présente, PHP lève une `UnhandledMatchError` — impossible d'oublier silencieusement un cas.

---

#### 3.4. Exercice ludique — Pierre, Feuille, Ciseaux

Créez le jeu **Pierre-Feuille-Ciseaux** ✊✋✌️ :

1. Un enum adossé `Coup: string` avec les cas `Pierre`, `Feuille`, `Ciseaux`.
2. Une méthode `bat(Coup $autre): bool` dans l'enum, écrite avec un `match` (la pierre bat les ciseaux, les ciseaux battent la feuille, la feuille bat la pierre).
3. Une méthode `emoji(): string` (match again !).
4. Une méthode statique `aleatoire(): self` pour le coup de l'ordinateur (piochez dans `self::cases()`).
5. Une classe `Partie` qui fait s'affronter le joueur (coup passé en paramètre ou lu au hasard) et l'ordinateur en 5 manches, affiche chaque manche et le score final :

```text
Manche 1 : Vous ✊ vs 🤖 ✌️ → vous gagnez !
Manche 2 : Vous ✋ vs 🤖 ✋ → égalité…
...
🏆 Score final : Vous 3 — 1 Ordinateur. GG !
```

Bonus : ajoutez `Lezard` et `Spock` (version *Big Bang Theory* 🖖) — vous mesurerez à quel point l'enum + `match` rend l'extension facile.

> 🎮 **La suite du chapitre 3** : [le feu de signalisation, le menu de la cantine et la chasse aux chaînes magiques](./exercices/chapitre-03.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 4. Les espaces de noms

Un **espace de noms** (*namespace*) est un moyen d'organiser et d'encapsuler les classes, fonctions et constantes — comme des dossiers pour vos fichiers. Il sert à éviter les **conflits de noms** : deux bibliothèques peuvent chacune avoir une classe `User` sans se marcher dessus.

La déclaration se fait avec le mot clé `namespace`, en **toute première instruction** du fichier :

```php
<?php

namespace App\Model;

class User
{
    // Code de la classe
}
```

Le **nom complet** de cette classe est désormais `App\Model\User`. Le séparateur est l'antislash `\`, et on utilise généralement un namespace par niveau de dossier (nous verrons pourquoi avec l'autoload).

Pour utiliser une classe d'un autre namespace, deux possibilités :

```php
<?php

namespace App\Controller;

// 1. Le nom pleinement qualifié (commence par \) :
$user = new \App\Model\User();

// 2. L'importation avec use (en haut du fichier), puis le nom court :
use App\Model\User;

$user = new User();
```

⚠️ Ne confondez pas les deux sens de `use` :

- `use App\Model\User;` **en haut d'un fichier** = importation d'un namespace ;
- `use MonTrait;` **dans le corps d'une classe** = utilisation d'un trait (chapitre [2.10](#210-les-traits)).

Si deux classes importées portent le même nom, on crée un **alias** avec `as` :

```php
use App\Model\User as UserModel;
use App\Entity\User as UserEntity;

$model = new UserModel();
$entity = new UserEntity();
```

Dernier détail utile : dans un fichier avec namespace, les classes natives de PHP (`Exception`, `PDO`, `DateTime`…) sont dans le namespace **global**, il faut donc les préfixer d'un `\` (ou les importer) :

```php
namespace App\Model;

throw new \Exception('Oups'); // sans le \, PHP chercherait App\Model\Exception !
```

> 🎮 **Exercices du chapitre 4** : [les deux guildes, le piège de l'Exception et le cartographe de projet](./exercices/chapitre-04.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 5. Auto-chargement des classes

L'auto-chargement (*autoloading*) permet de **charger automatiquement** les fichiers de classes au moment où l'on en a besoin, au lieu d'écrire des dizaines de `require_once` à la main.

On enregistre une fonction d'auto-chargement avec `spl_autoload_register()`. Cette fonction est appelée automatiquement par PHP dès qu'une classe inconnue est utilisée, et reçoit le **nom complet** de la classe (namespace compris) :

```php
spl_autoload_register(function (string $className) {
    // par exemple si le point d'entrée est dans le dossier public/
    $file = '../model/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Instanciation : PHP appelle notre fonction, qui charge ../model/App/Model/User.php
$user = new App\Model\User();

// ou avec une importation :
use App\Model\User;
$user2 = new User();
```

L'astuce centrale : `str_replace('\\', '/', $className)` transforme le namespace en chemin de dossier. C'est pour cela que l'on fait correspondre **l'arborescence des namespaces à celle des dossiers** — c'est le principe de la norme [PSR-4](https://www.php-fig.org/psr/psr-4/).

Voici une version plus complète, ancrée à la racine du projet :

```php
// Dans config.php à la racine du projet :
// const RACINE_PATH = __DIR__;

spl_autoload_register(function (string $class) {
    $file = RACINE_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
```

La fonction d'auto-chargement est généralement définie dans un fichier séparé (`autoload.php`), inclus une seule fois dans le contrôleur frontal (`public/index.php`) :

```php
// public/index.php
require_once __DIR__ . '/../autoload.php';

$user = new App\Model\User(); // tout se charge tout seul !
```

Organisation typique d'un petit projet MVC :

```text
projet/
├── autoload.php
├── config.php
├── controller/
├── model/
│   └── App/
│       └── Model/
│           ├── User.php
│           └── UserManager.php
└── public/
    └── index.php   ← contrôleur frontal
```

> 💡 **Dans la vraie vie** : les projets professionnels utilisent [Composer](https://getcomposer.org/), qui génère l'autoloader PSR-4 pour vous (`composer dump-autoload`, puis `require 'vendor/autoload.php';`). Comprendre `spl_autoload_register()` vous permet de comprendre ce que Composer fait sous le capot.

> 🎮 **Exercices du chapitre 5** : [le grimoire auto-magique, l'espion de l'autoloader et un aperçu sous le capot de Composer](./exercices/chapitre-05.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 6. Les exceptions

Une **exception** est un objet qui représente une situation anormale survenue pendant l'exécution du script. Plutôt que de renvoyer `false` ou `null` et d'espérer que l'appelant pense à vérifier, on **lance** une exception : le programme s'arrête net… sauf si quelqu'un l'**attrape** et la gère.

Vous en avez déjà rencontré avec PDO : `PDOException` !

Pour lancer une exception, utilisez le mot clé `throw` :

```php
throw new Exception("Message de l'exception");
```

Une exception peut être attrapée par un bloc `try...catch` :

```php
try {
    // Code susceptible de lancer une exception
} catch (Exception $e) {
    // Code exécuté SI une exception de type Exception a été lancée
    echo $e->getMessage();
}
```

L'objet exception transporte des informations utiles : `getMessage()`, `getCode()`, `getFile()`, `getLine()`, `getTraceAsString()`.

On peut attraper **plusieurs types** d'exceptions, du plus précis au plus général, et ajouter un bloc `finally` qui s'exécute **dans tous les cas** :

```php
try {
    $pdo = new PDO($dsn, $user, $password);
    // ...
} catch (PDOException $e) {
    echo "Problème de base de données : " . $e->getMessage();
} catch (Exception $e) {
    echo "Autre problème : " . $e->getMessage();
} finally {
    echo "Ce message s'affiche toujours, exception ou pas.";
}
```

> 💡 Depuis PHP 8.0, on peut attraper plusieurs types en un seul bloc : `catch (PDOException | RuntimeException $e)`, et même attraper sans variable : `catch (PDOException)`.

##### La hiérarchie Throwable

En PHP moderne, tout ce qui peut être lancé implémente l'interface `Throwable` :

```text
Throwable
├── Error          ← erreurs du moteur PHP (TypeError, ValueError…)
└── Exception      ← exceptions "applicatives"
    ├── RuntimeException
    ├── InvalidArgumentException (via LogicException)
    ├── PDOException
    └── … et les vôtres !
```

##### Créer ses propres exceptions

Créer une exception personnalisée, c'est simplement **hériter** d'`Exception` (tout le chapitre 2 sert enfin !) :

```php
<?php

class DivisionParZeroException extends Exception
{
    public function __construct(string $message = "Division par zéro impossible")
    {
        parent::__construct($message);
    }
}

class Division
{
    public function diviser(float $a, float $b): float
    {
        if ($b == 0) {
            throw new DivisionParZeroException();
        }
        return $a / $b;
    }
}

$division = new Division();

try {
    echo $division->diviser(10, 0);
} catch (DivisionParZeroException $e) {
    echo $e->getMessage();
}
```

L'intérêt d'une classe dédiée : l'appelant peut réagir **différemment selon le type** d'exception — un `catch` pour l'erreur métier, un autre pour le reste.

---

#### 6.1. Exercice ludique — La machine à café

Modélisez la machine à café ☕ de la salle de pause :

1. Créez trois exceptions personnalisées : `PlusDeCafeException`, `PlusDeGobeletException`, `MontantInsuffisantException` (cette dernière reçoit le montant manquant et l'inclut dans son message).
2. Créez la classe `MachineACafe` :
    - propriétés privées : `$doses` (10 au départ), `$gobelets` (5 au départ), `$prix` (constante, 0.50) ;
    - méthode `commander(float $montantInsere): string` qui vérifie tout dans l'ordre (argent, gobelets, café), lance la bonne exception, ou décrémente les stocks et renvoie `"☕ Voici votre café ! (monnaie rendue : 0.50 €)"`.
3. Écrivez un scénario de test : une file de 7 collègues (tableau de montants insérés, certains radins) commande à la suite, dans un `try...catch` par commande — la machine ne doit **jamais** faire planter le script, chaque déception s'affiche poliment :

```text
Client 1 : ☕ Voici votre café ! (monnaie rendue : 0.50 €)
Client 2 : 💸 Il manque 0.30 € !
Client 3 : ☕ Voici votre café !
...
Client 7 : 😱 Plus de gobelets ! Revenez demain.
```

Bonus : un bloc `finally` affiche l'état des stocks après chaque commande.

> 🎮 **La suite du chapitre 6** : [le videur de la boîte de nuit, la fusée à étages et le quiz « qui attrape quoi ? »](./exercices/chapitre-06.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 7. Le match et les autres nouveautés PHP 8 utiles

Avant de passer à la base de données, un tour d'horizon de nouveautés PHP 8 que nous utiliserons dans nos classes.

##### L'expression match (PHP 8.0)

`match` est un `switch` modernisé : c'est une **expression** (elle renvoie une valeur), la comparaison est **stricte** (`===`), il n'y a pas de `break` à oublier, et un cas non géré lève une `UnhandledMatchError` :

```php
$niveau = 7;

$titre = match(true) {
    $niveau >= 10 => 'Légende 🏆',
    $niveau >= 5  => 'Aventurier ⚔️',
    $niveau >= 1  => 'Novice 🐣',
    default       => 'Spectateur 🍿',
};

echo $titre; // Aventurier ⚔️
```

Plusieurs valeurs peuvent partager un même résultat :

```php
$jour = 'samedi';
$type = match($jour) {
    'samedi', 'dimanche' => 'week-end 🎉',
    default              => 'boulot 😅',
};
```

##### La syntaxe de callable de première classe (PHP 8.1)

Pour transformer une fonction ou une méthode en objet `Closure`, fini les chaînes de caractères fragiles :

```php
$majuscules = strtoupper(...);           // au lieu de 'strtoupper'
echo $majuscules('bonjour');             // BONJOUR

$noms = array_map(fn(Animal $a) => $a->sePresenter(), $animaux);
$crier = $rex->aboyer(...);              // méthode d'objet capturée
```

##### new dans les initialiseurs (PHP 8.1)

On peut donner un objet comme valeur par défaut d'un paramètre :

```php
class Service
{
    public function __construct(
        private Logger $logger = new NullLogger(), // PHP 8.1
    ) {}
}
```

##### Les attributs (PHP 8.0)

Les **attributs** ajoutent des métadonnées structurées aux classes, méthodes et propriétés, avec la syntaxe `#[...]`. Vous en connaissez déjà trois : `#[\Override]`, `#[\AllowDynamicProperties]` et… vous en croiserez partout dans les frameworks (`#[Route('/users')]` en Symfony, par exemple). Ils remplacent les vieilles annotations en commentaires `/** @... */`.

##### Aperçu PHP 8.4 / 8.5

- **Objets paresseux** (PHP 8.4, *lazy objects*) : créer un objet dont l'initialisation coûteuse est différée jusqu'au premier accès — surtout utilisé par les frameworks et ORM.
- **Opérateur pipe `|>`** (PHP 8.5) : chaîner des transformations de gauche à droite :

```php
$resultat = ' Bonjour le monde '
    |> trim(...)
    |> strtoupper(...)
    |> str_split(...);
// équivalent à str_split(strtoupper(trim(' Bonjour le monde ')))
```

> 🎮 **Exercices du chapitre 7** : [le Choixpeau magique, la chaîne de fabrication, le GPS prudent et l'usine à arguments nommés](./exercices/chapitre-07.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 8. Le mapping de tables SQL en classes PHP

Vous savez déjà interroger une base avec PDO et récupérer des **tableaux associatifs**. Le mapping objet-relationnel consiste à représenter chaque table par une **classe**, et chaque ligne par un **objet** — pour profiter du typage, de la validation et des méthodes métier.

Prenons un exemple simple avec une table `user` :

```sql
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL
);
```

Nous allons créer une classe `User` qui représente la table `user` :

```php
class User
{
    // Propriétés typées : miroir des colonnes SQL
    private ?int $id;
    private string $name;
    private string $email;

    // Constructeur : $id nullable car inconnu avant l'INSERT
    public function __construct(?int $id, string $name, string $email)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setEmail($email);
    }

    // getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    // setters : le poste de contrôle des données
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = trim(strip_tags($name));
    }

    public function setEmail(string $email): void
    {
        $emailValide = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($emailValide === false) {
            throw new InvalidArgumentException("Email invalide : $email");
        }
        $this->email = $emailValide;
    }
}
```

Remarquez le setter d'email : `filter_var()` renvoie `false` en cas d'email invalide — on ne stocke jamais `false` dans la propriété, on lance une **exception** (chapitre 6 réinvesti !).

---

[Menu de navigation](#menu-de-navigation)

---


#### 8.1. Utilisation d'une classe abstraite pour l'hydratation

Écrire un constructeur qui liste toutes les colonnes devient vite pénible. On peut utiliser une classe abstraite parente qui **hydrate** automatiquement l'objet à partir d'un tableau associatif (exactement ce que renvoie `fetch(PDO::FETCH_ASSOC)` !) :

```php
<?php

abstract class AbstractModel
{
    // constructeur - appelé lors de l'instanciation
    public function __construct(array $tab = [])
    {
        // tentative d'hydratation des données
        $this->hydrate($tab);
    }

    // en partant d'un tableau associatif et de ses clefs,
    // on régénère le nom des setters existants
    protected function hydrate(array $assoc): void
    {
        foreach ($assoc as $clef => $valeur) {
            // date_naissance → setDateNaissance
            $methodeName = "set" . str_replace("_", "", ucwords($clef, '_'));
            // si le setter existe, on l'appelle
            if (method_exists($this, $methodeName)) {
                $this->$methodeName($valeur);
            }
        }
    }
}
```

Ce qui donne pour le cas `User` :

```php
class User extends AbstractModel
{
    private ?int $id = null;
    private string $name = '';
    private string $email = '';

    // plus besoin de constructeur : hérité d'AbstractModel !

    // getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    // setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = trim(strip_tags($name));
    }

    public function setEmail(string $email): void
    {
        $emailValide = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($emailValide === false) {
            throw new InvalidArgumentException("Email invalide : $email");
        }
        $this->email = $emailValide;
    }
}
```

Et l'utilisation devient magique :

```php
// ce tableau vient typiquement d'un $stmt->fetch(PDO::FETCH_ASSOC)
$row = ['id' => 1, 'name' => 'Aline', 'email' => 'aline@example.com'];

$user = new User($row);
echo $user->getName(); // Aline
```

> 💡 C'est le principe de base de tous les **ORM** (Doctrine, Eloquent…) : transformer automatiquement des lignes SQL en objets, et inversement.

> 🎮 **Exercices du chapitre 8** : [la bibliothèque interdite, le traducteur snake_case et le paquet de cartes](./exercices/chapitre-08.md)

---

[Menu de navigation](#menu-de-navigation)

---


### 9. Les Managers

Un **manager** (aussi appelé *repository* ou *DAO*) est une classe qui centralise les requêtes SQL d'une table. Il fait le lien entre les objets et la base de données : la classe `User` représente les **données**, le `UserManager` sait les **lire et écrire** en base.

Cette séparation des responsabilités est essentielle : un objet `User` ne doit pas savoir qu'une base de données existe !

Nous allons créer une classe `UserManager` qui manipule les données de la table `user` :

```php
class UserManager
{
    // injection de dépendance : le manager REÇOIT sa connexion,
    // il ne la crée pas lui-même (promotion de propriété, PHP 8.0)
    public function __construct(
        private PDO $pdo,
    ) {}

    public function getUser(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // fetch renvoie false si aucune ligne : on renvoie null (type ?User)
        return $row === false ? null : new User($row);
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM user');
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }
        return $users;
    }

    public function addUser(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user (name, email) VALUES (:name, :email)'
        );
        $stmt->execute([
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
        ]);
        // on récupère l'id auto-incrémenté et on met l'objet à jour
        $user->setId((int) $this->pdo->lastInsertId());
    }

    public function updateUser(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE user SET name = :name, email = :email WHERE id = :id'
        );
        $stmt->execute([
            'id'    => $user->getId(),
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }

    public function deleteUser(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
```

Utilisation complète — tout le cours se rejoint ici :

```php
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=cours_poo;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $manager = new UserManager($pdo);

    // CREATE
    $aline = new User(['name' => 'Aline', 'email' => 'aline@example.com']);
    $manager->addUser($aline);
    echo "Créée avec l'id {$aline->getId()}" . PHP_EOL;

    // READ
    foreach ($manager->getUsers() as $user) {
        echo "- {$user->getName()} <{$user->getEmail()}>" . PHP_EOL;
    }

    // UPDATE
    $aline->setName('Aline la Grande');
    $manager->updateUser($aline);

    // DELETE
    $manager->deleteUser($aline->getId());

} catch (PDOException $e) {
    echo "Erreur base de données : " . $e->getMessage();
} catch (InvalidArgumentException $e) {
    echo "Donnée invalide : " . $e->getMessage();
}
```

---

#### 9.1. Exercice ludique — Le Gardien de Créatures

Vous êtes engagé·e comme gardien·ne d'un refuge pour créatures fantastiques. 🐉

1. Créez la table :

```sql
CREATE TABLE creature (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    espece VARCHAR(50) NOT NULL,     -- 'dragon', 'licorne', 'gobelin', 'phenix'
    niveau_danger INT NOT NULL DEFAULT 1,  -- de 1 à 5
    est_endormie TINYINT(1) NOT NULL DEFAULT 0
);
```

2. Créez un **enum adossé** `Espece: string` avec un `emoji(): string` et un `cri(): string` (match !).
3. Créez la classe `Creature extends AbstractModel` : propriétés, getters, setters — le setter d'espèce convertit la chaîne SQL en enum avec `Espece::from()`, le setter de niveau de danger le borne entre 1 et 5.
4. Créez le `CreatureManager` complet : `getCreature`, `getCreatures`, `addCreature`, `updateCreature`, `deleteCreature`, plus deux méthodes bonus :
    - `getCreaturesDangereuses(): array` (niveau ≥ 4) ;
    - `berceuse(int $id): void` qui passe `est_endormie` à 1 (UPDATE ciblé).
5. Scénario à jouer dans un script `refuge.php` :
    - peupler le refuge avec 5 créatures ;
    - afficher le registre : `🐉 Fumerolle (dragon) — danger 5/5 — 😴 endormie` ;
    - endormir toutes les créatures dangereuses avec `berceuse()` ;
    - un gobelin s'échappe (DELETE) — affichez un avis de recherche ! 

Bonus : ajoutez `getStatistiques(): array` qui renvoie le nombre de créatures par espèce (GROUP BY) et affichez un rapport digne d'un ministère de la magie.

> 🎮 **La suite du chapitre 9** : [les records de la borne d'arcade, la transaction du marchand et le manager générique](./exercices/chapitre-09.md) — avec une astuce SQLite pour travailler sans installer MySQL.

---

[Menu de navigation](#menu-de-navigation)

---


### 10. Mini-projet final — L'Arène des Héros

Le projet qui rassemble **tout** le cours : classes, héritage, interfaces, traits, enums, exceptions, autoload, hydratation, manager PDO. ⚔️🏟️

##### Le pitch

Les stagiaires de la promo créent chacun leur héros, les héros s'affrontent dans l'arène, et les résultats sont sauvegardés en base de données. À la fin : un classement général et une remise de trophées.

##### Cahier des charges

**Base de données**

```sql
CREATE TABLE hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    classe VARCHAR(20) NOT NULL,          -- 'guerrier', 'mage', 'voleur'
    pv INT NOT NULL DEFAULT 100,
    force_attaque INT NOT NULL DEFAULT 10,
    victoires INT NOT NULL DEFAULT 0,
    defaites INT NOT NULL DEFAULT 0
);
```

**Côté objets** (dossier `model/`, avec namespace `App\Model` et autoload) :

1. enum adossé `ClasseHero: string` (`Guerrier`, `Mage`, `Voleur`) avec `emoji()` et `bonusAttaque(): int` ;
2. interface `Combattant` (`attaquer()`, `recevoirDegats()`, `estVivant()`) ;
3. classe abstraite `Hero extends AbstractModel implements Combattant` : propriétés communes, hydratation, `__toString()` ;
4. trois classes enfants avec un pouvoir spécial chacune (redéfinition + `#[\Override]`) :
    - `Guerrier` : 10 % de chances de coup critique (dégâts ×2) ;
    - `Mage` : boule de feu dévastatrice mais 1 tour de recharge sur 2 ;
    - `Voleur` : 25 % de chances d'esquiver une attaque ;
5. exceptions personnalisées : `HeroMortException`, `ArenaVideException` ;
6. `HeroManager` (CRUD + `getClassement(): array` trié par victoires) ;
7. classe `Arene` : méthode `combat(Hero $a, Hero $b): Hero` qui affiche le déroulé tour par tour, met à jour victoires/défaites **en base** via le manager, et renvoie le vainqueur.

**Côté script** (`public/index.php`) :

- créer 4 héros (un par stagiaire !) et les enregistrer ;
- lancer un tournoi (2 demi-finales + finale) ;
- afficher le classement final :

```text
🏟️  BIENVENUE DANS L'ARÈNE  🏟️

⚔️ Demi-finale 1 : Ragnar 🪓 vs Merlin 🔮
   Tour 1 : Ragnar frappe (12 dégâts) — Merlin : 88 PV
   Tour 2 : Merlin lance une BOULE DE FEU (25 dégâts) — Ragnar : 75 PV
   ...
   💀 Ragnar tombe au combat. Merlin l'emporte !

🏆 CLASSEMENT GÉNÉRAL 🏆
1. Merlin 🔮 — 2 victoires, 0 défaite
2. Ragnar 🪓 — 1 victoire, 1 défaite
...
```

##### Barème d'auto-évaluation

| Critère | Points |
|---------|--------|
| Structure (namespaces, autoload, dossiers) | /4 |
| Modèles : typage, encapsulation, hydratation | /4 |
| Héritage, interface, `#[\Override]`, pouvoirs spéciaux | /4 |
| Enum + match | /2 |
| Exceptions personnalisées bien utilisées | /2 |
| Manager PDO : requêtes préparées, CRUD, classement | /4 |
| **Le fun du déroulé des combats** 🎭 | **/(bonus infini)** |

> 🎮 **Pour aller plus loin** : [six quêtes annexes pour enrichir l'Arène, deux projets alternatifs (le Gestionnaire de Potions, la Médiathèque) et la checklist de rendu](./exercices/chapitre-10.md)

---

[Menu de navigation](#menu-de-navigation)

---


### Annexe — Mémo des nouveautés objet PHP 8.0 à 8.5

| Fonctionnalité | Version | Exemple minimal |
|----------------|---------|-----------------|
| Promotion de propriétés | 8.0 | `__construct(private string $nom) {}` |
| Arguments nommés | 8.0 | `new Pizza(ananas: true)` |
| Expression `match` | 8.0 | `match($x) { 1, 2 => 'a', default => 'b' }` |
| Opérateur nullsafe | 8.0 | `$user->adresse?->ville` |
| Types union | 8.0 | `int\|float $force` |
| Attributs | 8.0 | `#[Route('/users')]` |
| Type de retour `static` | 8.0 | `public static function create(): static` |
| Énumérations | 8.1 | `enum Statut: string { case Publie = 'publie'; }` |
| Propriétés `readonly` | 8.1 | `public readonly string $id;` |
| Callable de 1re classe | 8.1 | `$f = strtoupper(...);` |
| `new` dans les initialiseurs | 8.1 | `function __construct($log = new NullLogger())` |
| Constantes `final` | 8.1 | `final const X = 1;` |
| Classes `readonly` | 8.2 | `readonly class Point { ... }` |
| Constantes dans les traits | 8.2 | `trait T { const X = 1; }` |
| Propriétés dynamiques dépréciées | 8.2 | `#[\AllowDynamicProperties]` pour y déroger |
| Constantes de classe typées | 8.3 | `public const string NOM = 'x';` |
| `#[\Override]` | 8.3 | vérifie qu'une méthode redéfinit bien le parent |
| `__clone()` et `readonly` | 8.3 | réinitialisation autorisée pendant le clonage |
| Property hooks | 8.4 | `public int $pv { set(int $v) { ... } }` |
| Visibilité asymétrique | 8.4 | `public private(set) int $score;` |
| `new` sans parenthèses | 8.4 | `new MaClasse()->methode()` |
| Objets paresseux | 8.4 | initialisation différée (frameworks/ORM) |
| Opérateur pipe | 8.5 | `$x \|> trim(...) \|> strtoupper(...)` |
| `final` sur propriétés promues | 8.5 | `__construct(public final string $nom)` |

---

[Menu de navigation](#menu-de-navigation)

---
