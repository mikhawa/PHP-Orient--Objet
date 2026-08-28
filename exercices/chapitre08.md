# Chapitre 8 — Le mapping de tables SQL en classes PHP

> 📖 Cours : [8. Le mapping de tables SQL en classes PHP](../README.md#8-le-mapping-de-tables-sql-en-classes-php)

⚠️ Ces exercices ne touchent **pas encore** à la base de données : on travaille avec des tableaux associatifs « comme si » ils venaient d'un `fetch(PDO::FETCH_ASSOC)`. Les vraies requêtes, c'est le chapitre 9 !

---

## 📚 Exercice 8.A — La bibliothèque interdite ⭐⭐

Voici la table à mapper :

```sql
CREATE TABLE livre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    annee_publication INT NOT NULL,
    est_interdit TINYINT(1) NOT NULL DEFAULT 0,
    note_moyenne DECIMAL(3,1) DEFAULT NULL
);
```

1. Créez la classe `Livre` avec des propriétés **typées** correspondant aux colonnes (attention : `id` et `note_moyenne` peuvent être `null`, `est_interdit` est un `bool` côté PHP mais un `0/1` côté SQL).
2. Écrivez un getter et un setter par propriété. Les setters sont vos **gardes du corps** :
    - `setTitre()` : `trim()` + `strip_tags()`, et lance une `InvalidArgumentException` si le titre est vide ;
    - `setAnneePublication()` : refuse une année avant 1450 (imprimerie !) ou dans le futur ;
    - `setEstInterdit()` : accepte `int`, `string` ou `bool` en entrée (SQL renvoie souvent `"1"`) et stocke un vrai `bool` — pensez à `(bool)` ;
    - `setNoteMoyenne()` : accepte `null`, sinon borne entre 0 et 10.
3. Ajoutez `__toString()` :

```text
📕 « Necronomicon » — Abdul Alhazred (1228) ⛔ INTERDIT — note 9.5/10
📗 « Apprendre la POO » — M. Pitz (2026) — pas encore noté
```

4. Testez avec ces trois lignes « venues de SQL », dont une bien pourrie :

```php
$lignes = [
    ['id' => 1, 'titre' => '  Necronomicon  ', 'auteur' => 'Abdul Alhazred',
     'annee_publication' => 1228, 'est_interdit' => '1', 'note_moyenne' => '9.5'],
    ['id' => 2, 'titre' => 'Apprendre la POO', 'auteur' => 'M. Pitz',
     'annee_publication' => 2026, 'est_interdit' => 0, 'note_moyenne' => null],
    ['id' => 3, 'titre' => '<script>alert("piraté")</script>', 'auteur' => 'Hacker',
     'annee_publication' => 3025, 'est_interdit' => 0, 'note_moyenne' => 99],
];
```

La troisième ligne doit être **refusée proprement** par une exception (année impossible), et le `<script>` ne doit jamais survivre à votre setter. Enveloppez la boucle dans un `try...catch`.

**Bonus** ⭐⭐ : ajoutez `estUnClassique(): bool` (publié il y a plus de 50 ans) et `age(): int`.

---

## 🔄 Exercice 8.B — Le traducteur snake_case → camelCase ⭐⭐⭐

Le cœur de l'hydratation, c'est cette ligne du cours :

```php
$methodeName = "set" . str_replace("_", "", ucwords($clef, '_'));
```

1. **Décortiquez-la** : écrivez un petit script qui affiche le résultat de chaque étape pour la clé `annee_publication` :

```text
'annee_publication'
→ ucwords(..., '_')      : 'Annee_Publication'
→ str_replace('_', '')   : 'AnneePublication'
→ "set" . ...            : 'setAnneePublication'
```

2. Créez votre propre classe abstraite `AbstractModel` avec `__construct(array $tab = [])` et `hydrate(array $assoc): void`.
3. Faites hériter votre `Livre` de `AbstractModel` et supprimez son constructeur. Vérifiez que `new Livre($ligne)` fonctionne toujours.
4. **Piège à explorer** : ajoutez à votre tableau une clé qui n'a pas de setter, par exemple `'colonne_fantome' => 'boo'`. Que fait `hydrate()` ? Pourquoi le `method_exists()` est-il indispensable ?
5. **Amélioration** ⭐⭐⭐ : ajoutez une méthode `toArray(): array` qui fait le **chemin inverse** (objet → tableau associatif prêt pour un `INSERT`) : parcourez les getters avec `get_class_methods($this)`, ne gardez que ceux qui commencent par `get`, et reconstruisez les clés en `snake_case` (indice : `strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $nom))`).

```php
$livre = new Livre(['titre' => 'Dune', 'auteur' => 'Herbert', 'annee_publication' => 1965]);
print_r($livre->toArray());
// ['id' => null, 'titre' => 'Dune', 'auteur' => 'Herbert', 'annee_publication' => 1965, ...]
```

---

## 🃏 Exercice 8.C — Le paquet de cartes (mapping + enum) ⭐⭐

1. Créez deux enums adossés : `Couleur: string` (`Pique`, `Coeur`, `Carreau`, `Trefle`) avec `emoji(): string`, et `Valeur: int` (`Deux = 2` … `As = 14`) avec `libelle(): string`.
2. Créez la classe `Carte` (`readonly`, avec promotion de propriétés) qui combine une `Couleur` et une `Valeur`, plus `__toString()` → `♥️ Dame de Cœur`.
3. Créez `Paquet` : constructeur qui génère les **52 cartes** (double boucle sur `Couleur::cases()` et `Valeur::cases()`), `melanger(): void`, `piocher(): ?Carte`, `nombreDeCartesRestantes(): int`.
4. Le setter qui compte : dans une classe `CartePersistee extends AbstractModel`, écrivez `setCouleur(string $valeur)` qui convertit la chaîne SQL en enum avec `Couleur::from()` — c'est **exactement** ce que vous ferez avec de vraies données au chapitre 9.
5. Simulez une bataille : deux joueurs piochent chacun une carte, le plus fort gagne, en 10 manches. 🃏

**Bonus** : que se passe-t-il si la base contient `'pique '` (avec une espace) et que vous appelez `Couleur::from()` ? Comment blinder votre setter ?

---

[⬅️ Retour à l'index des exercices](./README.md)
