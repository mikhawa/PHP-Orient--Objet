# Chapitre 1 — Les classes et les objets

> 📖 Cours : [1. Les classes et les objets](../README.md#1-les-classes-et-les-objets)

Les exercices 1.A (🐣 Tamagotchi), 1.B (🦸 fabrique de super-héros) et 1.C (🔐 coffre-fort) se trouvent [dans le cours, section 1.15](../README.md#115-exercices-ludiques--série-1). Faites-les d'abord ! Voici la suite.

---

## 🎲 Exercice 1.D — Le lanceur de dés parlant ⭐

Créez une classe `De` :

1. Propriété `public readonly int $faces`, initialisée par le constructeur (6 par défaut).
2. Propriété privée `$dernierLancer` (`?int`, `null` au départ).
3. Méthode `lancer(): int` qui utilise `random_int(1, $this->faces)`, mémorise et renvoie le résultat.
4. Méthode magique `__toString()` :
    - si le dé n'a jamais été lancé : `🎲 Dé à 6 faces (jamais lancé)` ;
    - sinon : `🎲 Dé à 20 faces → 17`.
5. Deux **fabriques statiques** : `De::classique()` (6 faces) et `De::deDonjon()` (20 faces).

Scénario de test :

```php
$d6 = De::classique();
$d20 = De::deDonjon();

echo $d6 . PHP_EOL;      // jamais lancé
$d6->lancer();
echo $d6 . PHP_EOL;      // → résultat

// Best of 5 : qui du d6 ou du d20 gagne le plus de manches ? (spoiler…)
```

**Bonus** ⭐⭐ : méthode `lancerAvantage(): int` qui lance deux fois et garde le meilleur (les rôlistes comprendront 🧙).

---

## 🧪 Exercice 1.E — La jauge magique (property hooks, PHP 8.4) ⭐⭐

Créez une classe `Jauge` (pensez : barre de vie, de mana, de batterie…) :

1. Constructeur : `nom` (string) et `max` (int, readonly, 100 par défaut).
2. Propriété `public int $valeur` avec un **hook `set`** qui borne automatiquement entre 0 et `$this->max` — aucune méthode setter !
3. Propriété **virtuelle** `public float $pourcentage` avec un hook `get` (calculée, jamais stockée).
4. Méthode `barre(): string` qui dessine la jauge sur 10 cases :

```php
$vie = new Jauge('Vie', 100);
$vie->valeur = 130;          // le hook intercepte → 100
$vie->valeur -= 65;
echo $vie->barre();          // Vie [███░░░░░░░] 35 %
echo $vie->pourcentage;      // 35
```

**Bonus** ⭐⭐⭐ : ajoutez une propriété `public private(set) int $nbModifications` (visibilité asymétrique) incrémentée par le hook `set` de `$valeur` — lisible partout, modifiable nulle part ailleurs que dans la classe.

---

## 🔮 Exercice 1.F — Quiz : « Que va afficher ce code ? » ⭐

Pour chaque extrait, **écrivez votre prédiction en commentaire AVANT d'exécuter**, puis vérifiez. Un point par bonne réponse, comptez votre score !

```php
// --- Extrait 1 ---
class Boite { public string $contenu = 'vide'; }
$a = new Boite();
$b = $a;
$b->contenu = 'trésor';
echo $a->contenu; // ?

// --- Extrait 2 ---
$c = clone $a;
$c->contenu = 'chaussette';
echo $a->contenu; // ?

// --- Extrait 3 ---
class Portefeuille {
    public function __construct(public readonly float $solde) {}
}
$p = new Portefeuille(50.0);
$p->solde = 1000000.0; // ?

// --- Extrait 4 ---
class Chrono {
    public static int $tics = 0;
    public function tic(): void { self::$tics++; }
}
$c1 = new Chrono();
$c2 = new Chrono();
$c1->tic();
$c2->tic();
$c2->tic();
echo Chrono::$tics; // ?

// --- Extrait 5 ---
class Fantome {}
$f = new Fantome();
$f->boo = 'BOUH'; // ? (PHP 8.2+ : lisez bien ce qui s'affiche…)
```

**Score** : 5/5 = 🧙 architecte objet · 3-4 = ⚔️ apprenti solide · 0-2 = 🐣 relisez le chapitre 1, il ne vous en voudra pas.

---

[⬅️ Retour à l'index des exercices](./README.md)
