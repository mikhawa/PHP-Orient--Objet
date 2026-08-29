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
<summary><b>Bonus — Les corrigés détaillés</b></summary>

- **La bibliothèque interdite** : setters qui valident, données SQL sales, `strip_tags` ≠ anti-XSS — [`08a-bibliotheque-interdite.php`](../solutions/chapitre-08/08a-bibliotheque-interdite.php)
- **Le traducteur `snake_case`** : gérer `annee_publication` → `setAnneePublication`, et le chemin inverse — [`08b-hydratation.php`](../solutions/chapitre-08/08b-hydratation.php)
- **Le paquet de cartes** : combiner enums et objets — [`08c-paquet-de-cartes.php`](../solutions/chapitre-08/08c-paquet-de-cartes.php)

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
