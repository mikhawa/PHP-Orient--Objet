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
<summary><b>Bonus 1 — Le Gardien de Créatures</b> (CRUD complet, enum en base, <code>GROUP BY</code>)</summary>

Un refuge pour créatures fantastiques, sur **SQLite en mémoire** (aucune installation).

**1. Un enum `Espece: string`** (`Dragon = 'dragon'`, `Licorne`, `Gobelin`, `Phenix`) avec `emoji()` et `cri()`.

**2. Un modèle `Creature extends AbstractModel`** (la classe abstraite d'hydratation du chapitre 8) :
`?int $id`, `string $name`, `Espece $espece`, `int $niveauDanger`, `bool $estEndormie`.
- `setEspece(Espece|string $e)` fait la conversion chaîne → enum (`Espece::tryFrom(strtolower(trim($e))) ?? throw ...`) ;
- `setNiveauDanger()` **borne** entre 1 et 5 ;
- `setEstEndormie()` normalise en `bool`.

**3. Un `CreatureManager`** qui **reçoit** sa connexion (`public function __construct(private PDO $pdo) {}` — injection de dépendance, pour pouvoir le tester avec une base en mémoire). Méthodes CRUD, **toutes en requêtes préparées** :

| Méthode | SQL |
|---------|-----|
| `getCreature(int $id): ?Creature` | `SELECT * FROM creature WHERE id = :id` → `null` si absent |
| `getCreatures(): array` | `SELECT * ... ORDER BY niveau_danger DESC, name` |
| `addCreature(Creature $c): void` | `INSERT` ; puis `$c->setId((int) $this->pdo->lastInsertId())` |
| `updateCreature(Creature $c): void` | `UPDATE ... WHERE id = :id` |
| `deleteCreature(int $id): void` | `DELETE ... WHERE id = :id` |
| `getCreaturesDangereuses(int $seuil = 4)` | `WHERE niveau_danger >= :seuil` |
| `berceuse(int $id): void` | `UPDATE creature SET est_endormie = 1 WHERE id = :id` |
| `getStatistiques(): array` | `SELECT espece, COUNT(*), AVG(niveau_danger), MAX(...) ... GROUP BY espece` |

⚠️ Aux frontières : `getEspece()->value` (enum → string) pour l'`INSERT`, `(int) $c->isEstEndormie()` (bool → 0/1).

**4. Un scénario complet** : créer la table, accueillir 5 pensionnaires (dont un annoncé au niveau 99 → borné à 5), afficher le registre, endormir les dangereuses, en supprimer une (« avis de recherche »), afficher le rapport `GROUP BY`.

> 💡 Notez en commentaire : pour passer à MySQL, **seul le DSN change** (`new PDO('mysql:host=...;dbname=...', 'root', '')`) et `INTEGER PRIMARY KEY AUTOINCREMENT` devient `INT AUTO_INCREMENT PRIMARY KEY`. Le modèle, le manager et les requêtes sont identiques.

</details>

<details>
<summary><b>Bonus 2 — Les records de la borne d'arcade</b> (<code>LIMIT</code> paramétré, <code>LIKE</code> sûr, injection SQL démontrée)</summary>

**1. Un modèle `Score`** : `?int $id`, `string $pseudo` (3 lettres majuscules — `setPseudo` fait `strtoupper`, `preg_replace('/[^A-Z0-9]/', '', ...)`, `substr(..., 0, 3)`), `int $points` (≥ 0, sinon exception), `string $jeu`, `string $joueLe`.

**2. Un `ScoreManager`** avec `add()`, `getById()`, `getByJeu()`, `delete()`, et surtout :

- **`getTop(int $limite = 10): array`** — le piège du `LIMIT` : on ne concatène pas, mais `execute(['limite' => $n])` passe la valeur en **chaîne** et MySQL refuse `LIMIT '10'`. Solution :

  ```php
  $stmt = $this->pdo->prepare('SELECT * FROM score ORDER BY points DESC LIMIT :limite');
  $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);   // on FORCE le type entier
  $stmt->execute();
  ```

- **`chercherParPseudo(string $p): array`** — `LIKE` sécurisé : les `%` font partie de la **valeur**, pas de la requête :

  ```php
  $stmt->execute(['motif' => '%' . $p . '%']);
  ```

- **`statistiquesParJeu(): array`** — `GROUP BY jeu` avec `COUNT(*)`, `MAX(points)`, `AVG(points)`.

**3. La démonstration d'injection SQL** — cœur de l'exercice. Cherchez le pseudo `' OR 1=1 --` :

- via `chercherParPseudo()` (requête préparée) → **0 résultat**, l'attaque est traitée comme du texte ;
- via une concaténation `"SELECT * FROM score WHERE pseudo LIKE '%$malveillant%'"` → **toute la table** sort. Décomposez l'attaque en commentaire (la quote ferme la chaîne, `OR 1=1` est toujours vrai, `--` commente la fin).

**Conclusion** : requête préparée. Toujours. Sans exception.

</details>

<details>
<summary><b>Bonus 3 — La transaction du marchand</b> (<code>beginTransaction</code> / <code>commit</code> / <code>rollBack</code> : tout ou rien)</summary>

Acheter un objet = **deux écritures** qui doivent réussir ensemble ou échouer ensemble : débiter l'or du joueur **et** ajouter l'objet à son inventaire. Une panne entre les deux laisserait un joueur débité sans objet.

**1. Deux tables** : `joueur (id, pseudo, or_possede)` et `inventaire (id, joueur_id, objet, prix)`.

**2. Une exception `OrInsuffisantException extends RuntimeException`** portant `public readonly int $manque`.

**3. Un `MarchandManager`** avec la méthode clé :

```php
public function acheter(int $joueurId, string $objet, int $prix): void
{
    $this->pdo->beginTransaction();
    try {
        // 1. lire l'or du joueur ; si introuvable ou insuffisant → throw
        // 2. UPDATE joueur SET or_possede = or_possede - :prix WHERE id = :id
        // 3. INSERT INTO inventaire (...)
        $this->pdo->commit();
    } catch (Throwable $e) {   // Throwable = Exception ET Error
        $this->pdo->rollBack();
        throw $e;              // ← on RELANCE : ne jamais avaler l'exception
    }
}
```

Plus `getOr(int $id): int` et `getInventaire(int $id): array`.

**4. Trois scénarios à jouer :**

- achat normal → l'or baisse, l'objet apparaît ;
- objet trop cher (`OrInsuffisantException`) → **vérifiez que l'or n'a pas bougé** ;
- panne SQL au milieu (ajoutez un paramètre `bool $saboter = false` qui fait un `INSERT` vers une colonne inexistante **après** le débit) → après le `rollBack`, l'or est intact, aucun objet ajouté.

**Les 3 pièges à noter** : oublier le `rollBack()` (connexion bloquée), attraper sans relancer (l'appelant croit que tout va bien), et en MySQL les tables MyISAM ne gèrent pas les transactions (il faut InnoDB).

</details>

<details>
<summary><b>Bonus 4 — Le manager générique</b> (factoriser le CRUD dans une classe abstraite)</summary>

Objectif : n'écrire `findAll`, `findById`, `deleteById`, `count` **qu'une seule fois**, pour tous les managers.

**1. Une classe `AbstractManager`**

```php
abstract class AbstractManager
{
    public function __construct(protected PDO $pdo) {}

    abstract protected function getTable(): string;
    abstract protected function getModelClass(): string;   // ex. Score::class

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM ' . $this->getTable());
        $classe = $this->getModelClass();
        return array_map(fn(array $row) => new $classe($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
        //                              ↑ instanciation DYNAMIQUE
    }

    public function findById(int $id): ?AbstractModel { /* prepare + new $classe($row) */ }
    public function deleteById(int $id): void { /* ... */ }
    public function count(): int { /* SELECT COUNT(*) ... */ }
}
```

**2. Deux modèles** `Score` et `Creature` (`extends AbstractModel`), avec leurs setters.

**3. Deux managers ultra-légers** : chacun ne fournit que `getTable()` et `getModelClass()` (marqués `#[\Override]`), plus **ses** méthodes spécifiques (`ScoreManager::getTop()`, `CreatureManager::getDangereuses()`).

**4. Démonstration** : `findAll()`, `count()`, `findById()`, `deleteById()` hérités, fonctionnant sur **deux tables différentes** sans une ligne de code CRUD dupliquée.

**5. La question de sécurité (essentielle)** : `findAll()` fait `'SELECT * FROM ' . $this->getTable()` — une **concaténation**, ce qu'on s'interdit partout ailleurs ! Pourquoi est-ce acceptable **ici** et jamais avec une donnée utilisateur ?

- SQL n'autorise **pas** de placeholder pour un identifiant (`SELECT * FROM :table` est une erreur de syntaxe) → la requête préparée ne peut rien pour un nom de table ;
- `getTable()` renvoie une **chaîne littérale écrite par le développeur**, jamais une saisie ;
- si on doit vraiment choisir une table dynamiquement, la seule méthode sûre est la **liste blanche** :

  ```php
  $tables = ['score' => 'score', 'creature' => 'creature'];
  $table = $tables[$saisie] ?? throw new InvalidArgumentException();
  ```

Notez aussi les limites : `findAll()` charge tout en mémoire, pas de jointures, la clé primaire est supposée s'appeler `id`. C'est là que commencent les vrais ORM (Doctrine…).

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
