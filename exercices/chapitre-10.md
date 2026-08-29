# Chapitre 10 — Le projet final

> 📖 Cours : [10. Mini-projet final](../README.md#10-mini-projet-final--larène-des-héros)

Le projet rassemble tout ce que vous avez vu. Il est découpé en **6 étapes** : faites-les dans l'ordre, et testez après chacune.

---

# ⚔️ L'Arène des Héros

Des héros s'affrontent, et les résultats sont enregistrés en base.

---

## Étape 1 — La classe `Hero` ⭐

Créez `arene.php` avec :

```php
<?php

class Hero
{
    public function __construct(
        private string $nom,
        private int $pv = 100,
        private int $force = 10,
    ) {}

    public function getNom(): string { return $this->nom; }
    public function getPv(): int { return $this->pv; }

    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    public function recevoirDegats(int $degats): void
    {
        $this->pv = max(0, $this->pv - $degats);
    }

    public function attaquer(Hero $cible): void
    {
        $degats = random_int(1, $this->force);
        echo $this->nom . ' frappe (' . $degats . ' dégâts)' . PHP_EOL;
        $cible->recevoirDegats($degats);
    }
}
```

**Testez :**

```php
$a = new Hero('Ragnar');
$b = new Hero('Merlin');

$a->attaquer($b);
echo $b->getNom() . ' : ' . $b->getPv() . ' PV' . PHP_EOL;
```

✅ **Vous devez voir une attaque et les PV diminuer avant de continuer.**

---

## Étape 2 — Le combat ⭐

**À faire :** ajoutez cette fonction et lancez un combat complet.

```php
function combat(Hero $a, Hero $b): Hero
{
    while ($a->estVivant() && $b->estVivant()) {
        $a->attaquer($b);

        if ($b->estVivant()) {
            $b->attaquer($a);
        }
    }

    return $a->estVivant() ? $a : $b;
}

$vainqueur = combat(new Hero('Ragnar'), new Hero('Merlin'));
echo '🏆 ' . $vainqueur->getNom() . ' gagne !' . PHP_EOL;
```

✅ Le combat doit se terminer et annoncer un vainqueur.

---

## Étape 3 — Trois classes de héros ⭐⭐

Utilisez l'**héritage** (chapitre 2).

**À faire :**

1. Rendez `Hero` **abstraite** et remplacez `attaquer()` par une méthode abstraite `calculerDegats(): int`, appelée depuis `attaquer()` :

```php
abstract class Hero
{
    // ... le reste ne change pas ...

    abstract protected function calculerDegats(): int;

    public function attaquer(Hero $cible): void
    {
        $degats = $this->calculerDegats();
        echo $this->nom . ' attaque (' . $degats . ' dégâts)' . PHP_EOL;
        $cible->recevoirDegats($degats);
    }
}
```

⚠️ `$this->nom` est `private` : passez-le en `protected` pour que les enfants y accèdent.

2. Créez trois enfants, chacun avec son pouvoir :

- **`Guerrier`** : dégâts normaux, mais 1 fois sur 10, les dégâts sont **doublés** (coup critique).
- **`Mage`** : gros dégâts (`random_int(20, 35)`), mais **1 fois sur 2** il rate et fait 0 dégât.
- **`Voleur`** : dégâts normaux. Redéfinissez aussi `recevoirDegats()` : 1 fois sur 4, il **esquive** (ne perd rien).

💡 **Coup de pouce** pour « 1 fois sur 10 » : `if (random_int(1, 10) === 1) { ... }`

✅ Faites combattre un `Guerrier` contre un `Mage`.

---

## Étape 4 — L'enum des classes ⭐⭐

**À faire :** créez un enum (chapitre 3) qui servira à stocker la classe en base :

```php
enum ClasseHero: string
{
    case Guerrier = 'guerrier';
    case Mage = 'mage';
    case Voleur = 'voleur';

    public function emoji(): string
    {
        return match ($this) {
            ClasseHero::Guerrier => '🪓',
            ClasseHero::Mage     => '🔮',
            ClasseHero::Voleur   => '🗡️',
        };
    }
}
```

Ajoutez une méthode abstraite `getClasse(): ClasseHero` dans `Hero`, que chaque enfant implémente en renvoyant son cas.

---

## Étape 5 — La base de données ⭐⭐

**À faire :** créez la table et le manager (chapitre 9).

```php
$pdo = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$pdo->exec('
    CREATE TABLE hero (
        id        INTEGER PRIMARY KEY AUTOINCREMENT,
        nom       VARCHAR(100) NOT NULL,
        classe    VARCHAR(20) NOT NULL,
        victoires INT NOT NULL DEFAULT 0,
        defaites  INT NOT NULL DEFAULT 0
    )
');
```

Écrivez un `HeroManager` avec :

- `add(Hero $hero): void` — enregistre le héros (`$hero->getClasse()->value` pour la colonne `classe`) ;
- `getClassement(): array` — `SELECT * FROM hero ORDER BY victoires DESC` ;
- `enregistrerVictoire(int $id): void` et `enregistrerDefaite(int $id): void` — deux `UPDATE` simples.

---

## Étape 6 — Le tournoi 🏆

**À faire :** assemblez le tout.

1. Créez 4 héros et enregistrez-les.
2. Deux demi-finales, puis la finale.
3. Après chaque combat, enregistrez la victoire et la défaite.
4. Affichez le classement final.

**Résultat attendu (exemple) :**

```text
🏟️  L'ARÈNE DES HÉROS

── Demi-finale 1 ──
Ragnar attaque (12 dégâts)
Merlin attaque (28 dégâts)
...
🏆 Merlin gagne !

🏆 CLASSEMENT
1. 🔮 Merlin — 2 victoires, 0 défaite
2. 🪓 Ragnar — 1 victoire, 1 défaite
...
```

---

## ✅ Grille d'auto-évaluation

| Étape | Critère | Fait ? |
|-------|---------|--------|
| 1 | Une classe avec propriétés privées et getters | ☐ |
| 2 | Un combat qui se termine avec un vainqueur | ☐ |
| 3 | Classe abstraite + 3 enfants avec des pouvoirs différents | ☐ |
| 3 | Aucun `if` pour savoir quel héros attaque comment (polymorphisme) | ☐ |
| 4 | Un enum utilisé pour la classe du héros | ☐ |
| 5 | Un manager, avec uniquement des requêtes **préparées** | ☐ |
| 6 | Le tournoi complet, avec classement depuis la base | ☐ |
| — | Le combat vous a fait sourire au moins une fois 😄 | ☐ |

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Améliorations possibles</b></summary>

- **Les armes** : un enum `TypeArme` avec un bonus de dégâts, et une colonne `arme` en base.
- **Le journal de combat** : un trait `Journalisable` (méthodes `noter()` et `afficherJournal()`), utilisé par l'Arène **et** par le manager.
- **Les exceptions** : `HeroMortException` si on fait attaquer un héros déjà mort.
- **La transaction** : victoire et défaite enregistrées ensemble ou pas du tout (`beginTransaction` / `commit` / `rollBack`).
- **L'interface web** : un formulaire pour créer son héros, une page classement. Aucun changement dans vos classes — c'est la preuve que votre architecture est bonne. 🏗️

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
