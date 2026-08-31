# Chapitre 8 — Une table SQL, une classe PHP

> 📖 Cours : [8. Le mapping de tables SQL en classes PHP](../README.md#8-le-mapping-de-tables-sql-en-classes-php)

Vous savez déjà récupérer un **tableau associatif** avec PDO. On va le transformer en **objet**.

Pas de base de données dans ce chapitre : on travaille avec des tableaux « comme si » ils venaient d'un `fetch()`.

---

## 📇 Exercice 8.1 — Du tableau à l'objet ⭐

Voici une table et une ligne telle que PDO vous la renvoie :

```sql
CREATE TABLE user (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL
);
```

```php
// Ce que renvoie $stmt->fetch(PDO::FETCH_ASSOC)
$ligne = ['id' => 1, 'name' => 'Aline', 'email' => 'aline@example.com'];
```

**À faire :**

1. Créez une classe `User` avec trois propriétés **publiques** typées : `$id` (int), `$name` (string), `$email` (string).
2. Créez un objet et remplissez-le à partir du tableau :

```php
$user = new User();
$user->id = $ligne['id'];
$user->name = $ligne['name'];
$user->email = $ligne['email'];

echo $user->name . ' <' . $user->email . '>' . PHP_EOL;
```

**Résultat attendu :**

```text
Aline <aline@example.com>
```

---

## 🏗️ Exercice 8.2 — Avec un constructeur ⭐

Trois lignes d'affectation, c'est long.

**À faire :** ajoutez un constructeur à `User` (version courte de PHP 8) :

```php
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
```

⚠️ Supprimez les trois déclarations de propriétés du dessus : le constructeur les crée maintenant.

Créez l'objet en une ligne :

```php
$user = new User($ligne['id'], $ligne['name'], $ligne['email']);
```

---

## 🔒 Exercice 8.3 — Protéger l'email ⭐⭐

**À faire :**

1. Passez les trois propriétés en `private`.
2. Ajoutez les trois getters : `getId()`, `getName()`, `getEmail()`.
3. Ajoutez un **setter qui vérifie** l'email :

```php
    public function setEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Email invalide : ' . $email);
        }

        $this->email = $email;
    }
```

4. Dans le constructeur, appelez `$this->setEmail($email);` au lieu d'affecter directement.

**Testez avec un email valide, puis avec `pas-un-email` :**

```php
try {
    $mauvais = new User(2, 'Bob', 'pas-un-email');
} catch (InvalidArgumentException $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}
```

💡 **Voilà l'intérêt de la classe** : il devient **impossible** de créer un `User` avec un email invalide. Avec un tableau associatif, rien ne vous en empêchait.

---

## 🔁 Exercice 8.4 — Plusieurs lignes ⭐

Un `SELECT` renvoie plusieurs lignes.

**À faire :**

```php
$lignes = [
    ['id' => 1, 'name' => 'Aline', 'email' => 'aline@example.com'],
    ['id' => 2, 'name' => 'Bob',   'email' => 'bob@example.com'],
    ['id' => 3, 'name' => 'Carla', 'email' => 'carla@example.com'],
];

$users = [];

foreach ($lignes as $ligne) {
    $users[] = new User($ligne['id'], $ligne['name'], $ligne['email']);
}

foreach ($users as $user) {
    echo '- ' . $user->getName() . PHP_EOL;
}
```

💡 C'est **exactement** ce que fera votre `Manager` au chapitre 9, avec de vraies données.

---

## 🪄 Exercice 8.5 — L'hydratation automatique ⭐⭐

Écrire `$ligne['id'], $ligne['name'], $ligne['email']` devient pénible dès qu'il y a 15 colonnes. On peut automatiser.

**À faire :** recopiez cette classe, telle quelle :

```php
abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        foreach ($tab as $clef => $valeur) {
            // 'name' devient 'setName'
            $methode = 'set' . ucfirst($clef);

            if (method_exists($this, $methode)) {
                $this->$methode($valeur);
            }
        }
    }
}
```

Puis :

1. Faites hériter votre `User` de `AbstractModel` : `class User extends AbstractModel`.
2. **Supprimez** le constructeur de `User`.
3. Ajoutez les setters manquants : `setId()` et `setName()`.
4. Créez vos objets ainsi :

```php
$user = new User($ligne);      // le tableau entier, d'un coup !
```

**Résultat attendu** : le même qu'à l'exercice 8.4.

💡 **Comment ça marche** : la boucle transforme chaque clé du tableau en nom de setter (`name` → `setName`), vérifie que la méthode existe, et l'appelle. C'est le principe de base de tous les ORM comme Doctrine.

---

## 🕵️ Exercice 8.6 — Pourquoi le `method_exists` ? ⭐⭐

**À faire :** ajoutez une clé qui n'a pas de setter :

```php
$ligne = [
    'id' => 1,
    'name' => 'Aline',
    'email' => 'aline@example.com',
    'created_at' => '2026-01-01',      // ← pas de setCreatedAt() !
];

$user = new User($ligne);
echo $user->getName() . PHP_EOL;
```

**Questions :**

1. Est-ce que ça plante ?
2. Retirez le `if (method_exists(...))` de `AbstractModel` et relancez. Que se passe-t-il ?
3. Pourquoi cette ligne est-elle indispensable dans un vrai projet ?

💡 **Réponse à la question 3** : le jour où quelqu'un ajoute une colonne à la table, votre code PHP continue de fonctionner. Sans le test, un simple `ALTER TABLE` casserait toute l'application.

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus 1 — La bibliothèque interdite</b> (setters validateurs, données SQL sales, <code>strip_tags</code> ≠ anti-XSS)</summary>

**1. Une classe `Livre`**, miroir d'une table SQL, avec ces propriétés **privées et typées** :
`?int $id` (null tant que l'INSERT n'a pas eu lieu), `string $titre`, `string $auteur`, `int $anneePublication`, `bool $estInterdit`, `?float $noteMoyenne`.

**2. Le constructeur reçoit tout et passe TOUJOURS par les setters.** Comme les données viennent de SQL (qui renvoie souvent des chaînes), les setters acceptent des types larges :

```php
public function __construct(
    ?int $id,
    string $titre,
    string $auteur,
    int $anneePublication,
    bool|int|string $estInterdit = false,
    float|int|string|null $noteMoyenne = null,
) {
    $this->setId($id);
    $this->setTitre($titre);
    // ... etc
}
```

**3. Les setters « gardes du corps » :**

| Setter | Règle |
|--------|-------|
| `setId` | `null` autorisé ; sinon `>= 1`, sinon `throw new InvalidArgumentException` |
| `setTitre` | `trim(strip_tags($titre))` ; si vide → exception |
| `setAuteur` | idem |
| `setAnneePublication` | entre `1450` (Gutenberg) et l'année courante, sinon exception |
| `setEstInterdit` | `(bool) (int) $valeur` — normalise `"1"`/`"0"`/`1`/`true` |
| `setNoteMoyenne` | `null`/`''` → `null` ; sinon **bornée** à `[0, 10]` (on corrige, on ne rejette pas) |

Ajoutez `age(): int`, `estUnClassique(): bool` (> 50 ans) et `__toString()`.

**4. Testez avec 3 lignes « SQL »**, dont une avec `annee_publication => 3025` (doit être **rejetée**) et une avec `'<script>alert("xss")</script>Le Grand Livre` comme titre.

**5. Observez ce que les setters ont corrigé** : `'  Necronomicon  '` → `'Necronomicon'`, `'1'` → `true`, `'9.5'` → `9.5` (float), note `99` → `10.0`.

> 🔐 **Le point crucial à noter en commentaire** : `strip_tags('<script>alert("xss")</script>Titre')` donne `'alert("xss")Titre'` — les balises partent, **le texte reste**. `strip_tags()` **n'est pas** une protection XSS. La vraie protection est **à l'affichage** : `htmlspecialchars($titre, ENT_QUOTES, 'UTF-8')`. Et **rejeter** (exception) vs **corriger** (borner) : donnée aberrante = bug = exception ; donnée imprécise mais exploitable = correction.

</details>

<details>
<summary><b>Bonus 2 — Le traducteur snake_case → camelCase</b> (l'hydratation décortiquée, le chemin inverse)</summary>

**1. Décortiquez la « ligne magique »** de l'hydratation sur quelques exemples :

```php
$clef = 'annee_publication';
ucwords($clef, '_');            // 'Annee_Publication'
str_replace('_', '', ...);      // 'AnneePublication'
'set' . ...;                    // 'setAnneePublication'
```

**2. Une classe abstraite `AbstractModel`**

```php
abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        $this->hydrate($tab);
    }

    protected function hydrate(array $assoc): void
    {
        foreach ($assoc as $clef => $valeur) {
            $methode = 'set' . str_replace('_', '', ucwords((string) $clef, '_'));
            if (method_exists($this, $methode)) {   // ← INDISPENSABLE
                $this->$methode($valeur);
            }
        }
    }
}
```

**3. Une classe `Livre extends AbstractModel`** — **sans constructeur** (celui du parent fait tout), avec ses propriétés privées, ses getters (`getTitre`, `isEstInterdit`…) et ses setters. Hydratez-la avec un tableau qui ressemble à `$stmt->fetch(PDO::FETCH_ASSOC)`.

**4. Le piège de la colonne fantôme** : ajoutez `'colonne_fantome' => 'boo'` et `'created_at' => '2026-01-01'` au tableau. Aucune erreur : `hydrate()` calcule `setColonneFantome()`, ne la trouve pas, passe. **Retirez** le `method_exists()` et constatez le `Error: Call to undefined method`. C'est ce qui rend l'hydratation robuste face à un `ALTER TABLE`.

**5. Le chemin inverse — `toArray(): array`** dans `AbstractModel` : parcourt `get_class_methods($this)`, garde les `get*`/`is*`, retransforme `AnneePublication` → `annee_publication` (indice : `preg_replace('/(?<!^)[A-Z]/', '_$0', $nom)` puis `strtolower`), et renvoie `['titre' => 'Dune', 'annee_publication' => 1965, ...]` — un tableau directement passable à `$stmt->execute()`.

> ⚠️ Les noms de colonnes viennent de **vos getters**, jamais d'une saisie utilisateur — ne construisez jamais une requête ainsi à partir de `$_POST`.

</details>

<details>
<summary><b>Bonus 3 — Le paquet de cartes</b> (enums + <code>readonly</code>, frontière SQL ↔ objet)</summary>

**1. Deux enums adossés**

```php
enum Couleur: string {
    case Pique = 'pique'; case Coeur = 'coeur'; case Carreau = 'carreau'; case Trefle = 'trefle';
    public function emoji(): string { /* ♠️ ♥️ ♦️ ♣️ */ }
    public function libelle(): string { /* Pique, Cœur, Carreau, Trèfle */ }
}

enum Valeur: int {
    case Deux = 2; /* ... */ case Valet = 11; case Dame = 12; case Roi = 13; case As = 14;
    public function libelle(): string {
        // 'Valet'/'Dame'/'Roi'/'As' pour les figures, sinon (string) $this->value
    }
}
```

**2. Une `readonly class Carte`** (PHP 8.2 — une carte est immuable) avec `public Couleur $couleur`, `public Valeur $valeur` et `__toString()` → `"♥️ Dame de Cœur"`.

**3. Une classe `Paquet`** : le constructeur crée les **52 cartes** (`foreach Couleur::cases() as ... foreach Valeur::cases() ...`). Méthodes `melanger()` (`shuffle`), `piocher(): ?Carte` (`array_pop`, `null` si vide), `nombreDeCartesRestantes(): int`.

**4. Une bataille de 10 manches** : à chaque manche, on pioche 2 cartes et on compare `$carteA->valeur->value` vs `$carteB->valeur->value` (l'ordre est encodé dans l'enum, pas besoin de table de correspondance). Score final.

**5. La frontière SQL ↔ enum** : une classe `CartePersistee extends AbstractModel` (celle du bonus 2) dont le **setter fait la conversion** :

```php
public function setCouleur(Couleur|string $valeur): void
{
    if ($valeur instanceof Couleur) { $this->couleur = $valeur; return; }

    $normalisee = strtolower(trim($valeur));                 // blindage : 'PIQUE ' → 'pique'
    $this->couleur = Couleur::tryFrom($normalisee)
        ?? throw new InvalidArgumentException("Couleur inconnue : '$valeur'");
}
```

Et `toArray()` fait le retour : `'couleur' => $this->couleur->value` (le **scalaire**, pas l'enum — une colonne SQL ne stocke que des scalaires).

Testez avec `['couleur' => 'coeur', 'valeur' => '12']`, puis avec `'PIQUE '` (doit marcher grâce au `trim`+`strtolower`), puis avec `'losange'` (doit lever l'exception).

> 💡 L'idiome PHP 8 à retenir : `Enum::tryFrom($v) ?? throw new ...` — `throw` est une **expression** depuis PHP 8.0, utilisable à droite d'un `??`.

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
