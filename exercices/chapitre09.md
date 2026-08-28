# Chapitre 9 — Les Managers

> 📖 Cours : [9. Les Managers](../README.md#9-les-managers)

L'exercice 9.A (🐉 Le Gardien de Créatures) se trouve [dans le cours, section 9.1](../README.md#91-exercice-ludique--le-gardien-de-créatures). Faites-le d'abord ! Voici la suite.

💡 **Pas de MySQL sous la main ?** Tous ces exercices fonctionnent avec **SQLite**, sans installation :

```php
$pdo = new PDO('sqlite:' . __DIR__ . '/arcade.sqlite', options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
// puis $pdo->exec("CREATE TABLE IF NOT EXISTS ...");
```

(En SQLite, remplacez `INT AUTO_INCREMENT PRIMARY KEY` par `INTEGER PRIMARY KEY AUTOINCREMENT`.)

---

## 🕹️ Exercice 9.B — Les records de la borne d'arcade ⭐⭐

```sql
CREATE TABLE score (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(3) NOT NULL,        -- les 3 lettres de l'arcade !
    points INT NOT NULL DEFAULT 0,
    jeu VARCHAR(50) NOT NULL,
    joue_le DATETIME NOT NULL
);
```

1. Classe `Score extends AbstractModel` : le setter de pseudo force 3 caractères **en majuscules** (`strtoupper` + `substr`), celui de points refuse les valeurs négatives.
2. Classe `ScoreManager` avec injection de PDO par le constructeur (promotion de propriété !) et les méthodes :
    - `add(Score $score): void` — n'oubliez pas `lastInsertId()` ;
    - `getById(int $id): ?Score` ;
    - `getTop(int $limite = 10): array` — 🚨 **piège** : on ne met **jamais** une variable directement dans la requête, même un `LIMIT`. Utilisez `bindValue(':limite', $limite, PDO::PARAM_INT)` et expliquez en commentaire pourquoi `LIMIT :limite` avec `execute([...])` ne marche pas toujours ;
    - `getByJeu(string $jeu): array` ;
    - `delete(int $id): void`.
3. Peuplez la borne avec une dizaine de scores et affichez le tableau des records :

```text
🕹️  HALL OF FAME — PAC-POO  🕹️
🥇 AAA ......... 999 999 pts
🥈 BOB ......... 512 340 pts
🥉 XYZ ......... 128 000 pts
 4. PHP .........  42 000 pts
```

4. **Sécurité** : ajoutez `chercherParPseudo(string $pseudo): array` avec un `LIKE`. Testez-la en passant `' OR 1=1 --` comme pseudo. Que se passe-t-il grâce à la requête préparée ? Écrivez (juste pour voir, puis supprimez !) la version concaténée et constatez la catastrophe. 💣

**Bonus** ⭐⭐⭐ : `statistiquesParJeu(): array` avec un `GROUP BY` renvoyant nombre de parties, meilleur score et moyenne par jeu.

---

## 🧾 Exercice 9.C — La transaction du marchand ⭐⭐⭐

Un joueur achète un objet : il faut **débiter son or** ET **ajouter l'objet à son inventaire**. Si une des deux opérations échoue, l'autre ne doit **surtout pas** rester appliquée !

```sql
CREATE TABLE joueur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL,
    or_possede INT NOT NULL DEFAULT 100
);
CREATE TABLE inventaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    joueur_id INT NOT NULL,
    objet VARCHAR(100) NOT NULL,
    prix INT NOT NULL
);
```

1. Créez `OrInsuffisantException`.
2. Dans `MarchandManager`, écrivez `acheter(int $joueurId, string $objet, int $prix): void` :
    - `$this->pdo->beginTransaction();`
    - vérifier l'or du joueur → si insuffisant, `throw new OrInsuffisantException(...)` ;
    - `UPDATE joueur SET or_possede = or_possede - :prix WHERE id = :id` ;
    - `INSERT INTO inventaire ...` ;
    - `$this->pdo->commit();`
    - dans un `catch`, `$this->pdo->rollBack();` puis **relancez** l'exception (le manager signale, il ne décide pas à la place de l'appelant).
3. Testez trois scénarios : achat réussi, achat trop cher (vérifiez avec un `SELECT` que l'or n'a **pas** bougé), et un achat où vous provoquez volontairement une erreur SQL au milieu (par exemple en insérant dans une colonne inexistante) — l'or doit être restauré.

```text
💰 Aline a 100 or
🛒 Achat d'une « Épée en mousse » (30 or) → ✅ Il reste 70 or
🛒 Achat d'un « Dragon apprivoisé » (5000 or) → ⛔ Il te manque 4930 or !
💰 Vérification : Aline a toujours 70 or (transaction annulée)
```

---

## 🏗️ Exercice 9.D — Le manager générique ⭐⭐⭐

Vos managers se ressemblent tous comme deux gouttes d'eau. Factorisez !

1. Créez une classe **abstraite** `AbstractManager` :
    - constructeur `(protected PDO $pdo)` ;
    - deux méthodes **abstraites** : `getTable(): string` et `getModelClass(): string` ;
    - des méthodes concrètes qui les utilisent : `findAll(): array`, `findById(int $id): ?object`, `deleteById(int $id): void`, `count(): int`.
2. Dans `findAll()`, instanciez dynamiquement le bon modèle :

```php
$classe = $this->getModelClass();
$objets[] = new $classe($ligne);   // hydratation !
```

3. Faites hériter `ScoreManager extends AbstractManager` : il ne définit plus que `getTable()` (→ `'score'`), `getModelClass()` (→ `Score::class`) et ses méthodes **spécifiques** (`getTop()`, `statistiquesParJeu()`).
4. Faites de même avec un deuxième manager (reprenez `CreatureManager` de l'exercice 9.A) : vérifiez que vous n'avez presque plus rien à écrire. 🎉
5. **Question de réflexion** : quelle est la limite de cette approche ? (Indice : le nom de la table ne doit **jamais** venir d'une saisie utilisateur — pourquoi ?)

---

[⬅️ Retour à l'index des exercices](./README.md)
