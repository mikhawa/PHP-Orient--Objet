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
<summary><b>Amélioration 1 — Les armes</b> (enum + colonne SQL)</summary>

Chaque héros porte une arme qui modifie ses dégâts.

- Créez un `enum TypeArme: string` (`Epee = 'epee'`, `Hache = 'hache'`, `Dague = 'dague'`, `Baton = 'baton'`) avec une méthode `bonusDegats(): int` (par ex. épée +3, hache +6 mais…, dague +1, bâton +0) et `emoji(): string`.
- Ajoutez une colonne `arme VARCHAR(20)` à la table `hero`, une propriété `TypeArme $arme` au modèle, son getter/setter (le setter convertit la chaîne SQL en enum : `TypeArme::tryFrom(...) ?? ...`).
- Dans `attaquer()`, ajoutez `$this->arme->bonusDegats()` aux dégâts de base.
- Adaptez l'`INSERT`/`UPDATE` du manager (`->getArme()->value`) et l'hydratation.

**Test** : deux héros identiques, l'un avec une hache, l'autre avec une dague → celui à la hache doit gagner nettement plus souvent.

</details>

<details>
<summary><b>Amélioration 2 — Le journal de combat</b> (un trait partagé)</summary>

- Créez un `trait Journalisable` avec une propriété privée `array $journal = []` et deux méthodes : `noter(string $ligne): void` (ajoute au journal, horodaté avec `date('H:i:s')`) et `afficherJournal(): string` (renvoie tout le journal, une ligne par entrée).
- Faites `use Journalisable;` **à la fois** dans le service `Arene` **et** dans le `HeroManager` — deux classes sans lien de parenté qui partagent le même comportement (c'est tout l'intérêt d'un trait).
- Dans `Arene::combat()`, appelez `$this->noter("...")` à chaque tour ; dans `HeroManager::enregistrerResultat()`, notez chaque écriture en base.
- À la fin du programme, affichez les deux journaux côte à côte.

</details>

<details>
<summary><b>Amélioration 3 — Les exceptions métier</b></summary>

- Créez `App\Exception\HeroMortException extends \RuntimeException`, avec un constructeur `public function __construct(public readonly string $nomDuHero)` qui compose le message `"💀 Aria est déjà tombé au combat."`.
- Dans `Hero::attaquer()` (ou `recevoirDegats()`), levez-la si `$this->pv <= 0` : un héros mort ne peut plus frapper.
- Dans `Arene::combat()`, entourez la boucle de combat d'un `try/catch (HeroMortException $e)` qui termine proprement la manche.
- Créez aussi `ArenaVideException` levée si on tente de faire combattre un héros contre lui-même (`if ($a === $b)`).

</details>

<details>
<summary><b>Amélioration 4 — La transaction</b> (victoire + défaite : tout ou rien)</summary>

Enregistrer un résultat = **deux** `UPDATE` : `+1 victoire` pour l'un, `+1 défaite` pour l'autre. Ils doivent réussir ensemble.

```php
public function enregistrerResultat(Hero $vainqueur, Hero $perdant): void
{
    $this->pdo->beginTransaction();
    try {
        $this->incrementer($vainqueur->getId(), 'victoires');
        $this->incrementer($perdant->getId(), 'defaites');
        $this->pdo->commit();
    } catch (\Throwable $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}
```

**Test** : passez un `getId()` invalide pour le perdant → après le `rollBack`, le vainqueur ne doit **pas** avoir gagné sa victoire non plus.

</details>

<details>
<summary><b>Amélioration 5 — L'interface web</b></summary>

Sans toucher à vos classes `Hero`, `HeroManager`, `Arene` :

- une page `public/creer.php` : un `<form method="post">` avec le nom et un `<select>` de classes (généré depuis `ClasseHero::cases()`), qui appelle `$manager->add(new Guerrier([...]))` puis redirige ;
- une page `public/classement.php` : un `<table>` HTML alimenté par `$manager->getClassement()`, chaque valeur **échappée** avec `htmlspecialchars(...)` ;
- un `public/index.php` qui lance un tournoi et affiche le résultat.

Si tout fonctionne sans modifier une seule ligne de vos modèles/managers, c'est la preuve que la séparation « logique métier / affichage » est réussie. 🏗️

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
