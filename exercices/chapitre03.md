# Chapitre 3 — Les énumérations (PHP 8.1)

> 📖 Cours : [3. Les énumérations](../README.md#3-les-énumérations-php-81)

L'exercice 3.A (✊✋✌️ Pierre-Feuille-Ciseaux) se trouve [dans le cours, section 3.4](../README.md#34-exercice-ludique--pierre-feuille-ciseaux). Faites-le d'abord ! Voici la suite.

---

## 🚦 Exercice 3.B — Le feu de signalisation ⭐

1. Créez un enum pur `Feu` avec les cas `Rouge`, `Orange`, `Vert`.
2. Ajoutez la méthode `suivant(): self` : Vert → Orange → Rouge → Vert (un `match`, évidemment).
3. Ajoutez `duree(): int` (Vert 30 s, Orange 5 s, Rouge 25 s) et `emoji(): string` (🟢 🟠 🔴).
4. Simulez 2 cycles complets :

```text
🟢 Vert pendant 30 s
🟠 Orange pendant 5 s
🔴 Rouge pendant 25 s
🟢 Vert pendant 30 s
...
Durée totale de la simulation : 120 s
```

**Bonus** ⭐⭐ : méthode `peutPasser(bool $estUnTaxiPresse = false): bool` — le taxi pressé passe aussi à l'orange (à ne pas reproduire en vrai 😅).

---

## 🍽️ Exercice 3.C — Le menu de la cantine ⭐⭐

1. Créez un **enum adossé** `Jour: int` avec `Lundi = 1` … `Dimanche = 7`.
2. Méthodes (au `match`) :
    - `menu(): string` — lundi boulettes-frites 🇧🇪, mardi pâtes, … samedi et dimanche : `"La cantine est fermée, débrouillez-vous"` (regroupez les deux cas sur une seule ligne du match) ;
    - `estOuvert(): bool`.
3. Le programme lit un numéro de jour depuis la ligne de commande :

```php
// php cantine.php 3
$numero = (int) ($argv[1] ?? 0);
```

4. Utilisez `Jour::tryFrom($numero)` : si le résultat est `null`, affichez `"Le jour 42 n'existe pas dans ce plan de réalité 🌀"` — sinon affichez le menu du jour.

```text
$ php cantine.php 1
Lundi : 🍟 Boulettes sauce lapin & frites
$ php cantine.php 7
Dimanche : La cantine est fermée, débrouillez-vous
$ php cantine.php 42
Le jour 42 n'existe pas dans ce plan de réalité 🌀
```

**Bonus** ⭐⭐ : une méthode statique `aujourdhui(): self` qui utilise `(int) date('N')` et `from()`. Affichez le menu du jour réel.

---

## 🧟 Exercice 3.D — Avant / après : la chasse aux chaînes magiques ⭐⭐

Voici du code « d'avant les enums » :

```php
function degatsSort(string $element): int
{
    if ($element == 'feu') return 30;
    if ($element == 'glace') return 20;
    if ($element == 'foudre') return 25;
    return 0; // element inconnu ? tant pis, 0 dégât, personne ne le saura…
}

echo degatsSort('Feu');    // 0 ?! (majuscule)
echo degatsSort('foudr');  // 0 ?! (typo)
```

1. Listez les problèmes de ce code (il y en a au moins trois).
2. Réécrivez-le avec un enum adossé `Element: string` et une méthode `degats(): int`.
3. Montrez que la typo devient désormais **impossible** : que se passe-t-il avec `Element::from('foudr')` ?
4. Ajoutez `faiblesse(): self` (le feu craint la glace, la glace craint la foudre, la foudre craint le feu) et affichez le triangle des éléments complet en bouclant sur `Element::cases()`.

---

[⬅️ Retour à l'index des exercices](./README.md)
