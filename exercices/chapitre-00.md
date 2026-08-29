# Chapitre 0 — Du procédural à l'objet

> 📖 Cours : [0. Du procédural à l'objet](../README.md#0-du-procédural-à-lobjet)

Objectif de ce chapitre : voir la différence entre un **tableau** et un **objet**. Rien de plus.

---

## 🎵 Exercice 0.1 — Lisez et exécutez ⭐

**Ne codez rien.** Copiez ce fichier, lancez-le, et observez.

```php
<?php

// ── Version 1 : avec un tableau (ce que vous savez déjà faire)
$chanson = ['titre' => 'PHP Anthem', 'artiste' => 'The Coders'];

echo $chanson['titre'] . ' — ' . $chanson['artiste'] . PHP_EOL;


// ── Version 2 : avec un objet (la nouveauté)
class Chanson
{
    public string $titre = '';
    public string $artiste = '';
}

$chanson2 = new Chanson();
$chanson2->titre = 'PHP Anthem';
$chanson2->artiste = 'The Coders';

echo $chanson2->titre . ' — ' . $chanson2->artiste . PHP_EOL;
```

**Questions** (répondez en commentaire dans le fichier) :

1. Les deux versions affichent-elles la même chose ?
2. Quel signe utilise-t-on pour lire une case de **tableau** ? Et une propriété d'**objet** ?

💡 `['titre']` d'un côté, `->titre` de l'autre. C'est la principale différence d'écriture à retenir.

---

## ➕ Exercice 0.2 — Ajoutez une propriété ⭐

Reprenez le fichier de l'exercice 0.1.

1. Ajoutez une propriété `public int $duree = 0;` dans la classe `Chanson`.
2. Donnez-lui la valeur `210`.
3. Affichez-la.

**Résultat attendu :**

```text
PHP Anthem — The Coders (210 secondes)
```

---

## 🎤 Exercice 0.3 — Créez une deuxième chanson ⭐

Toujours dans le même fichier.

1. Créez un **deuxième objet** `Chanson` avec `new`, appelé `$chanson3`.
2. Donnez-lui le titre `Boucle infinie`, l'artiste `While Trio` et la durée `240`.
3. Affichez les deux chansons, l'une après l'autre.

**Résultat attendu :**

```text
PHP Anthem — The Coders (210 secondes)
Boucle infinie — While Trio (240 secondes)
```

💡 **Ce qu'il faut comprendre ici** : vous avez écrit la classe **une seule fois**, et vous avez créé **deux objets** avec. La classe est le moule, les objets sont les gaufres. 🧇

---

## 🐛 Exercice 0.4 — La faute de frappe ⭐

Un petit test pour voir l'intérêt des objets.

1. Dans votre fichier, écrivez volontairement une faute :

```php
echo $chanson['titer'];      // tableau, avec une faute
```

Lancez. Que se passe-t-il ? Le script s'arrête-t-il ?

2. Maintenant, la même faute sur un objet :

```php
echo $chanson2->titer;       // objet, avec la même faute
```

Lancez. Le message est-il différent ?

3. Enfin, une faute sur un **appel de méthode** (on en écrira au chapitre suivant) :

```php
$chanson2->afficher();       // cette méthode n'existe pas
```

Là, PHP s'arrête net avec une **erreur fatale**.

**À retenir** : plus l'erreur est visible tôt et fort, mieux c'est. Une faute de frappe silencieuse qui fausse un calcul est bien pire qu'un programme qui s'arrête en vous disant exactement ce qui ne va pas.

---

## 🚀 Pour aller plus loin

Si les quatre exercices ci-dessus vous ont paru faciles :

<details>
<summary><b>Exercice bonus — La playlist complète</b> (cliquez pour déplier)</summary>

Créez une classe `Playlist` avec :

- une propriété `public array $chansons = [];`
- une méthode `ajouter(Chanson $chanson)` qui ajoute une chanson au tableau
- une méthode `afficher()` qui les affiche toutes avec une boucle `foreach`
- une méthode `dureeTotale()` qui additionne les durées

Puis affichez la durée totale au format `mm:ss` (indice : `intdiv($total, 60)` et `$total % 60`).

Corrigé : [`solutions/chapitre-00/00a-playlist.php`](../solutions/chapitre-00/00a-playlist.php)

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
