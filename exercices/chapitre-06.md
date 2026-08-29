# Chapitre 6 — Les exceptions

> 📖 Cours : [6. Les exceptions](../README.md#6-les-exceptions)

Vous en avez déjà croisé une avec PDO : `PDOException`. Voyons comment ça marche, et comment créer les vôtres.

---

## 💣 Exercice 6.1 — Lancer et attraper ⭐

Recopiez et lancez :

```php
<?php

function diviser(int $a, int $b): float
{
    if ($b === 0) {
        throw new Exception('Division par zéro impossible !');
    }

    return $a / $b;
}

echo diviser(10, 2) . PHP_EOL;    // 5
echo diviser(10, 0) . PHP_EOL;    // 💥
```

**Question** : que se passe-t-il à la deuxième ligne ? Le programme continue-t-il ?

**À faire :** entourez le deuxième appel d'un `try...catch` :

```php
try {
    echo diviser(10, 0) . PHP_EOL;
} catch (Exception $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}

echo 'Le programme continue !' . PHP_EOL;
```

**Résultat attendu :**

```text
5
⛔ Division par zéro impossible !
Le programme continue !
```

💡 **À retenir** : `throw` lance l'alerte, `try...catch` l'attrape. Sans le `catch`, le programme s'arrête.

---

## 🧰 Exercice 6.2 — Ce que contient une exception ⭐

**À faire :** dans votre `catch`, affichez aussi :

```php
    echo 'Message : ' . $e->getMessage() . PHP_EOL;
    echo 'Ligne   : ' . $e->getLine() . PHP_EOL;
    echo 'Fichier : ' . $e->getFile() . PHP_EOL;
```

💡 Une exception n'est pas juste un message : c'est un **objet** avec des méthodes. `getLine()` vous dit exactement où le problème est survenu — pratique pour déboguer.

---

## 🏷️ Exercice 6.3 — Votre propre exception ⭐⭐

Créer une exception personnalisée, c'est simplement **hériter** d'`Exception` (chapitre 2 réutilisé !).

**À faire :**

1. Ajoutez cette classe en haut du fichier :

```php
class DivisionParZeroException extends Exception
{
}
```

Oui, elle est vide : elle hérite de tout.

2. Dans `diviser()`, remplacez `throw new Exception(...)` par :

```php
        throw new DivisionParZeroException('Division par zéro impossible !');
```

3. Dans le `catch`, remplacez `Exception` par `DivisionParZeroException`.

**Résultat attendu** : le même qu'avant.

**Question** : si vous remettez `catch (Exception $e)`, est-ce que ça marche encore ? Testez, et expliquez pourquoi.

💡 **Réponse** : oui ! Parce que `DivisionParZeroException` **est une** `Exception` (héritage). Un `catch` attrape le type demandé **et tous ses descendants**.

---

## ☕ Exercice 6.4 — La machine à café ⭐⭐

**À faire :** créez une classe `MachineACafe` :

- propriété **privée** `$gobelets`, valant `3` au départ ;
- une méthode `servir(): string` qui :
  - lance une exception si `$gobelets` vaut 0, avec le message `😱 Plus de gobelets !` ;
  - sinon, enlève 1 gobelet et renvoie `☕ Voici votre café !`.

**Programme de test :**

```php
$machine = new MachineACafe();

for ($client = 1; $client <= 5; $client++) {
    try {
        echo "Client $client : " . $machine->servir() . PHP_EOL;
    } catch (Exception $e) {
        echo "Client $client : " . $e->getMessage() . PHP_EOL;
    }
}
```

**Résultat attendu :**

```text
Client 1 : ☕ Voici votre café !
Client 2 : ☕ Voici votre café !
Client 3 : ☕ Voici votre café !
Client 4 : 😱 Plus de gobelets !
Client 5 : 😱 Plus de gobelets !
```

💡 **Ce qui compte ici** : la machine ne fait **jamais planter le programme**. Elle signale le problème, et le code appelant décide quoi en faire.

---

## 💰 Exercice 6.5 — Une exception qui transporte une info ⭐⭐

Une exception peut porter plus qu'un message.

**À faire :**

1. Ajoutez une exception personnalisée avec son propre constructeur :

```php
class MontantInsuffisantException extends Exception
{
    public function __construct(public readonly float $manque)
    {
        parent::__construct('💸 Il manque ' . $manque . ' € !');
    }
}
```

2. Ajoutez à votre machine une méthode `commander(float $argent)` : le café coûte `0.50 €`. Si le montant est insuffisant, lancez `new MontantInsuffisantException(0.50 - $argent)`.

3. Dans le `catch`, vous pouvez lire la donnée transportée :

```php
} catch (MontantInsuffisantException $e) {
    echo $e->getMessage() . PHP_EOL;
    echo 'Il vous manque exactement ' . $e->manque . ' €' . PHP_EOL;
}
```

💡 C'est tout l'intérêt d'une classe d'exception dédiée : elle transporte des **données utiles**, pas seulement du texte.

---

## 🚀 Pour aller plus loin

<details>
<summary><b>Bonus 1 — La hiérarchie d'exceptions (le videur)</b></summary>

Créez `RefusEntreeException`, puis trois enfants : `TropJeuneException`, `TenueNonReglementaireException`, `ListeNoireException`. Un **seul** `catch (RefusEntreeException $e)` les attrape toutes les trois.

Corrigé : [`06b-videur.php`](../solutions/chapitre-06/06b-videur.php)

</details>

<details>
<summary><b>Bonus 2 — Les autres exercices</b></summary>

- **La machine à café complète** : trois exceptions, `finally`, gestion des stocks — [`06a-machine-a-cafe.php`](../solutions/chapitre-06/06a-machine-a-cafe.php)
- **La fusée à étages** : attraper une exception et la relancer avec `previous` — [`06c-fusee.php`](../solutions/chapitre-06/06c-fusee.php)
- **Quiz** « qui attrape quoi ? » — [`06d-quiz.php`](../solutions/chapitre-06/06d-quiz.php)

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
