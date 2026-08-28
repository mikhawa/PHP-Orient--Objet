# Chapitre 7 — Le match et les autres nouveautés PHP 8

> 📖 Cours : [7. Le match et les autres nouveautés PHP 8 utiles](../README.md#7-le-match-et-les-autres-nouveautés-php-8-utiles)

---

## 🎩 Exercice 7.A — Le Choixpeau magique ⭐

Un nouveau stagiaire arrive à l'école de sorcellerie. Le Choixpeau doit le répartir.

1. Créez la classe `Choixpeau` avec la méthode `repartir(int $courage, int $ruse, int $sagesse, int $loyaute): string`.
2. **Interdiction d'utiliser `if`** : uniquement `match(true)`. La maison attribuée est celle du score le plus élevé (`max()` est votre ami).
3. Ajoutez `commentaire(string $maison): string` avec un `match` classique, une phrase par maison.
4. Cas particulier à gérer avec `match` : si tous les scores valent 0, le Choixpeau annonce `"🎩 Hmm… rien du tout ? Va donc à la cafétéria."`

```text
🎩 Le Choixpeau réfléchit…
   Courage 8 · Ruse 3 · Sagesse 5 · Loyauté 4
   « GRYFFONDOR ! » — Tu fonces d'abord, tu debug ensuite.
```

5. **Comparaison pédagogique** : réécrivez `commentaire()` avec un `switch` classique, puis répondez en commentaire : quelles sont les 3 différences avec `match` ? (valeur de retour, comparaison, `break`…)
6. Testez ce qui se passe si vous **supprimez le `default`** d'un `match` et passez une maison inconnue. Comment s'appelle l'erreur ?

---

## 🏭 Exercice 7.B — La chaîne de fabrication (callables de 1re classe) ⭐⭐

1. Créez une classe `Pipeline` avec :
    - une propriété privée `$etapes` (tableau de `Closure`) ;
    - `ajouter(callable $etape): static` qui empile l'étape et **renvoie `$this`** (chaînage fluide !) ;
    - `executer(mixed $valeur): mixed` qui applique toutes les étapes dans l'ordre.
2. Utilisez la **syntaxe de callable de première classe** (`strtoupper(...)`) pour construire une chaîne de nettoyage de pseudo :

```php
$nettoyeur = (new Pipeline())
    ->ajouter(trim(...))
    ->ajouter(strtolower(...))
    ->ajouter(fn(string $s) => str_replace(' ', '_', $s))
    ->ajouter(fn(string $s) => substr($s, 0, 15));

echo $nettoyeur->executer('   Le Grand Sorcier DU Code   ');
// le_grand_sorci
```

3. Créez une deuxième chaîne « générateur de cri de guerre » : mettre en majuscules, ajouter des `!!!`, répéter 2 fois.
4. **Bonus PHP 8.5** ⭐⭐⭐ : si votre PHP est en 8.5, réécrivez la première chaîne avec l'**opérateur pipe** `|>` et comparez la lisibilité :

```php
$resultat = '   Le Grand Sorcier   ' |> trim(...) |> strtolower(...);
```

(Vérifiez votre version avec `php -v` — en dessous de 8.5, contentez-vous du `Pipeline`, c'est le même principe fait maison !)

---

## 🧭 Exercice 7.C — Le GPS prudent (nullsafe) ⭐⭐

1. Créez trois classes minimalistes : `Pays` (`nom`), `Ville` (`nom`, `?Pays $pays`), `Utilisateur` (`pseudo`, `?Ville $ville`).
2. Créez trois utilisateurs : un complet (ville + pays), un avec une ville sans pays, un sans ville du tout.
3. Écrivez `localiser(Utilisateur $u): string` qui affiche le pays… **sans un seul `if`** et sans jamais planter — grâce à `?->` et à l'opérateur `??` :

```php
$pays = $u->ville?->pays?->nom ?? 'quelque part dans le multivers 🌀';
```

```text
Aline est en Belgique
Bob est quelque part dans le multivers 🌀
Carla est quelque part dans le multivers 🌀
```

4. Réécrivez la même logique **sans** nullsafe (avec des `if` imbriqués). Comptez les lignes des deux versions.
5. Piège à comprendre : que se passe-t-il avec `$u->ville->pays->nom` (sans `?`) pour Bob ? Notez le message d'erreur exact.

---

## 🎂 Exercice 7.D — L'usine à arguments nommés ⭐

1. Créez la classe `Gateau` avec 6 paramètres promus, tous avec une valeur par défaut : `base` ('génoise'), `garniture` ('chantilly'), `nappage` ('chocolat'), `bougies` (0), `messageEcrit` (''), `sansGluten` (false).
2. Ajoutez `__toString()` qui décrit le gâteau en une phrase gourmande.
3. Commandez 4 gâteaux **uniquement avec des arguments nommés**, en ne précisant que ce qui change à chaque fois — dont un gâteau d'anniversaire avec 30 bougies et un message.
4. Ajoutez `new` **dans un initialiseur** (PHP 8.1) : la classe `Commande` a un constructeur `(private Gateau $gateau = new Gateau())` — commander sans rien préciser donne le gâteau standard.
5. Question : essayez de passer un argument positionnel **après** un argument nommé (`new Gateau(garniture: 'fraise', 'génoise')`). Lisez l'erreur et retenez la règle.

---

[⬅️ Retour à l'index des exercices](./README.md)
