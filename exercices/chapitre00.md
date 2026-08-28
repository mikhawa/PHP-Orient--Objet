# Chapitre 0 — Du procédural à l'objet

> 📖 Cours : [0. Du procédural à l'objet](../README.md#0-du-procédural-à-lobjet)

---

## 🎵 Exercice 0.A — La playlist du stage ⭐

Voici du code **procédural** comme vous en avez déjà écrit :

```php
<?php

$playlist = [
    ['titre' => 'PHP Anthem',        'artiste' => 'The Coders',    'duree' => 210],
    ['titre' => 'Objets trouvés',    'artiste' => 'DJ Class',      'duree' => 185],
    ['titre' => 'Boucle infinie',    'artiste' => 'While Trio',    'duree' => 240],
];

function ajouterMorceau(array &$playlist, string $titre, string $artiste, int $duree): void
{
    $playlist[] = ['titre' => $titre, 'artiste' => $artiste, 'duree' => $duree];
}

function dureeTotale(array $playlist): int
{
    $total = 0;
    foreach ($playlist as $morceau) {
        $total += $morceau['duree'];
    }
    return $total;
}

function afficherPlaylist(array $playlist): void
{
    foreach ($playlist as $i => $morceau) {
        echo ($i + 1) . ". {$morceau['titre']} — {$morceau['artiste']}" . PHP_EOL;
    }
}
```

**Mission** : réécrire tout cela en orienté objet.

1. Créez une classe `Morceau` : propriétés `titre`, `artiste`, `duree` (en secondes), toutes initialisées via le constructeur (promotion de propriétés bienvenue).
2. Créez une classe `Playlist` : propriété privée `$morceaux` (tableau, vide au départ) et propriété `$nom`.
3. Ajoutez les méthodes `ajouter(Morceau $morceau): void`, `dureeTotale(): int` et `afficher(): void`.
4. Dans `afficher()`, la durée s'affiche en `mm:ss` (3:30 et pas 210 !).
5. Recréez la playlist ci-dessus en objets et vérifiez que tout fonctionne.

Sortie attendue (exemple) :

```text
🎵 Playlist « Pause café » — 3 morceaux, durée totale 10:35
1. PHP Anthem — The Coders (3:30)
2. Objets trouvés — DJ Class (3:05)
3. Boucle infinie — While Trio (4:00)
```

**Bonus** ⭐⭐ : ajoutez `morceauAleatoire(): Morceau` (mode shuffle 🔀) et `plusLong(): Morceau`.

---

## 🐛 Exercice 0.B — La chasse aux bugs ⭐

Le stagiaire précédent a laissé ce code procédural. Il « fonctionne »… mais affiche n'importe quoi :

```php
<?php

$panier = [
    ['produit' => 'Gaufre',    'prix' => 2.50, 'quantite' => 4],
    ['produit' => 'Chocolat',  'prix' => 3.00, 'quantite' => 2],
];

function totalPanier(array $panier): float
{
    $total = 0;
    foreach ($panier as $ligne) {
        $total += $ligne['prix'] * $ligne['qantite']; // 😈
    }
    return $total;
}

echo totalPanier($panier);
```

1. Exécutez le code. Que se passe-t-il ? Pourquoi PHP n'arrête-t-il pas tout avec une belle erreur fatale ?
2. Trouvez et corrigez le bug.
3. Réécrivez maintenant la même logique avec une classe `LignePanier` (propriétés **typées** `produit`, `prix`, `quantite`) et une classe `Panier` avec `ajouter()` et `total()`.
4. Reproduisez volontairement la faute de frappe version objet (`$ligne->qantite`). Comparez le comportement : c'est **exactement** pour ça qu'on passe à l'objet !

**Question de réflexion** (à écrire en commentaire) : citez deux autres avantages de la version objet par rapport au tableau associatif.

---

[⬅️ Retour à l'index des exercices](./README.md)
