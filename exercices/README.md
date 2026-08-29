# 🎮 Exercices par chapitre

Les exercices suivent le [cours](../README.md) chapitre par chapitre.

## Comment ils sont conçus

- **Une seule notion nouvelle par exercice.** On n'introduit jamais deux choses à la fois.
- **Le code de départ est fourni.** Vous recopiez, vous lancez, puis vous modifiez.
- **Chaque exercice tient en 2 ou 3 étapes**, avec le résultat attendu affiché.
- **Les exercices d'un chapitre se suivent** : gardez le même fichier et enrichissez-le.
- Les exercices plus ambitieux sont rangés en fin de chapitre, dans un bloc **« Pour aller plus loin »** dépliable.

Niveaux : ⭐ à faire par tout le monde · ⭐⭐ un peu plus exigeant

## Le sommaire

| Chapitre | Fichier | Ce qu'on y fait |
|----------|---------|-----------------|
| 0. Du procédural à l'objet | [chapitre-00.md](./chapitre-00.md) | Comparer un tableau et un objet |
| 1. Classes et objets | [chapitre-01.md](./chapitre-01.md) | Écrire sa première classe, pas à pas : propriétés, méthodes, `$this`, constructeur, `private`, getters, setters |
| 2. Héritage | [chapitre-02.md](./chapitre-02.md) | `extends`, redéfinition, `parent::`, classe abstraite, polymorphisme |
| 3. Énumérations | [chapitre-03.md](./chapitre-03.md) | `enum`, `match`, `cases()`, `from()` / `tryFrom()` |
| 4. Espaces de noms | [chapitre-04.md](./chapitre-04.md) | Deux classes de même nom, `use`, `as`, le piège de `\Exception` |
| 5. Auto-chargement | [chapitre-05.md](./chapitre-05.md) | Supprimer tous les `require` sauf un |
| 6. Exceptions | [chapitre-06.md](./chapitre-06.md) | `throw`, `try/catch`, créer ses propres exceptions |
| 7. `match` et PHP 8 | [chapitre-07.md](./chapitre-07.md) | `match`, `match(true)`, `?->`, arguments nommés |
| 8. Table SQL → classe | [chapitre-08.md](./chapitre-08.md) | Transformer une ligne PDO en objet, hydratation |
| 9. Managers | [chapitre-09.md](./chapitre-09.md) | Ranger ses requêtes dans une classe, CRUD complet, injection SQL |
| 10. Projet final | [chapitre-10.md](./chapitre-10.md) | L'Arène des Héros, en 6 étapes |

## Conseils de travail

- Un dossier par chapitre (`ex00/`, `ex01/`…), et lancez avec `php mon-fichier.php`.
- **Testez après chaque étape**, jamais à la fin. Un bug trouvé tout de suite se corrige en 30 secondes.
- Lisez les messages d'erreur **en entier** : PHP vous donne le fichier, la ligne et la raison.
- Bloqué·e plus de 15 minutes ? Relisez le chapitre du cours, l'explication y est.
