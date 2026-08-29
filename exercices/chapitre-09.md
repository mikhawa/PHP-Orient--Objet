# Chapitre 9 — Les Managers

> 📖 Cours : [9. Les Managers](../README.md#9-les-managers)

Un **manager** est une classe qui contient toutes les requêtes SQL d'une table. Vous savez déjà écrire ces requêtes : on va simplement les ranger.

💡 **Pas de MySQL sous la main ?** Utilisez SQLite, rien à installer :

```php
$pdo = new PDO('sqlite:' . __DIR__ . '/ma-base.sqlite', options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
```

(En SQLite, `INT AUTO_INCREMENT PRIMARY KEY` s'écrit `INTEGER PRIMARY KEY AUTOINCREMENT`.)

---

## 🗄️ Exercice 9.1 — Préparez le terrain ⭐

Créez `manager.php` avec la base et la table :

```php
<?php

$pdo = new PDO('sqlite::memory:', options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('
    CREATE TABLE user (
        id    INTEGER PRIMARY KEY AUTOINCREMENT,
        name  VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL
    )
');

$pdo->exec("INSERT INTO user (name, email) VALUES
    ('Aline', 'aline@example.com'),
    ('Bob', 'bob@example.com')");

// Vérification, à la manière procédurale que vous connaissez
$lignes = $pdo->query('SELECT * FROM user')->fetchAll(PDO::FETCH_ASSOC);
print_r($lignes);
```

Lancez : vous devez voir les deux lignes. **Rien de nouveau ici** — c'est le PDO que vous connaissez déjà.

---

## 📦 Exercice 9.2 — Votre premier manager ⭐

**À faire :** ajoutez la classe `User` (reprenez celle du chapitre 8, avec constructeur court), puis ce manager :

```php
class UserManager
{
    // Le manager REÇOIT la connexion, il ne la crée pas lui-même.
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM user');
        $users = [];

        while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($ligne['id'], $ligne['name'], $ligne['email']);
        }

        return $users;
    }
}
```

Utilisez-le :

```php
$manager = new UserManager($pdo);

foreach ($manager->getAll() as $user) {
    echo '- ' . $user->getName() . PHP_EOL;
}
```

**Résultat attendu :**

```text
- Aline
- Bob
```

💡 **Ce qui a changé** : `getAll()` ne renvoie plus des tableaux, mais des **objets `User`**. Le reste de votre application n'aura plus jamais affaire à des clés de tableau.

---

## 🔍 Exercice 9.3 — Chercher par id ⭐

**À faire :** ajoutez cette méthode au manager :

```php
    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ligne === false) {
            return null;      // aucun utilisateur avec cet id
        }

        return new User($ligne['id'], $ligne['name'], $ligne['email']);
    }
```

Testez avec un id qui existe, puis avec `999` :

```php
echo $manager->getById(1)?->getName() ?? 'introuvable';
echo PHP_EOL;
echo $manager->getById(999)?->getName() ?? 'introuvable';
```

**Résultat attendu :**

```text
Aline
introuvable
```

💡 Le `?User` du type de retour veut dire « un User **ou** null ». Et `?->` (chapitre 7) évite de planter quand c'est null.

---

## ➕ Exercice 9.4 — Ajouter un utilisateur ⭐⭐

**À faire :** ajoutez cette méthode :

```php
    public function add(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user (name, email) VALUES (:name, :email)'
        );

        $stmt->execute([
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }
```

Testez :

```php
$manager->add(new User(0, 'Carla', 'carla@example.com'));

foreach ($manager->getAll() as $user) {
    echo '- ' . $user->getName() . PHP_EOL;
}
```

Carla doit apparaître dans la liste.

**Question** : l'objet `$carla` que vous avez créé a l'id `0`, alors qu'en base il a reçu un vrai id. Comment le récupérer ?

💡 **Réponse** : `$this->pdo->lastInsertId()`. Ajoutez à la fin de `add()` :

```php
        $user->setId((int) $this->pdo->lastInsertId());
```

(Il vous faudra un setter `setId()` dans `User`.)

---

## ✏️ Exercice 9.5 — Modifier et supprimer ⭐⭐

**À faire :** écrivez vous-même les deux méthodes manquantes, sur le modèle des précédentes.

```php
    public function update(User $user): void
    {
        // UPDATE user SET name = :name, email = :email WHERE id = :id
    }

    public function delete(int $id): void
    {
        // DELETE FROM user WHERE id = :id
    }
```

Testez le cycle complet :

```php
$aline = $manager->getById(1);
$aline->setName('Aline la Grande');
$manager->update($aline);

echo $manager->getById(1)->getName() . PHP_EOL;   // Aline la Grande

$manager->delete(2);
echo count($manager->getAll()) . ' utilisateurs restants' . PHP_EOL;
```

🎉 Vous venez d'écrire un **CRUD complet** : Create, Read, Update, Delete.

---

## 🛡️ Exercice 9.6 — Pourquoi les requêtes préparées ⭐⭐

Un test de sécurité, à faire une fois dans sa vie.

**À faire :**

1. Ajoutez cette méthode **dangereuse** (à supprimer ensuite !) :

```php
    public function chercherDangereux(string $nom): array
    {
        $sql = "SELECT * FROM user WHERE name = '$nom'";   // 💣 concaténation
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
```

2. Appelez-la avec un nom normal, puis avec ceci :

```php
$resultats = $manager->chercherDangereux("' OR 1=1 --");
echo count($resultats) . ' résultats' . PHP_EOL;
```

**Questions :**

1. Combien de lignes obtenez-vous ? Alors qu'aucun utilisateur ne s'appelle ainsi ?
2. Écrivez la même méthode avec `prepare()` et `execute()`. Combien de lignes maintenant ?

3. **Supprimez la méthode dangereuse** de votre fichier.

💡 **À retenir** : avec une requête préparée, la valeur reste une **valeur**. Avec une concaténation, elle peut devenir du **code SQL**. C'est la faille la plus exploitée du web — et la plus simple à éviter.

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus — Les exercices détaillés</b></summary>

- **Le Gardien de Créatures** : CRUD complet avec enum, `GROUP BY`, scénario complet
- **La borne d'arcade** : `LIMIT` paramétré, injection SQL démontrée, statistiques
- **La transaction du marchand** : `beginTransaction`, `commit`, `rollBack`
- **Le manager générique** : factoriser le CRUD dans une classe abstraite

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
