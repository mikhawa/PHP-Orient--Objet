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
<summary><b>Bonus 1 — Le videur de la boîte de nuit</b> (hiérarchie d'exceptions, un seul <code>catch</code> pour une famille)</summary>

**1. Une famille d'exceptions**

```text
Exception
  └── RefusEntreeException            (la famille — juste "class ... extends Exception {}")
        ├── TropJeuneException
        ├── TenueNonReglementaireException
        └── ListeNoireException
```

- `TropJeuneException` : constructeur `public function __construct(public readonly int $age)` qui calcule `18 - $age` et compose un message `"18 ans minimum, reviens dans 3 ans !"` ;
- `TenueNonReglementaireException` : constructeur `public readonly string $delit`, message `"Les claquettes-chaussettes ne passeront pas."` ;
- `ListeNoireException` : message fixe `"Toi, on t'a reconnu. C'est NON."`.

**2. Une classe `Videur`**

- `public private(set) int $personnesControlees = 0;` ;
- `private const array TENUES_INTERDITES = ['claquettes-chaussettes', 'training', 'bob en jean'];` ;
- `controler(array $client): void` — le tableau `$client` a les clés `nom`, `age`, `tenue`, `blackliste`. Vérifie **dans cet ordre** : liste noire → âge → tenue, et `throw` l'exception correspondante ;
- `compter(): void` — incrémente le compteur.

**3. La file d'attente** — bouclez sur ~6 clients (dont un mineur, un blacklisté, un en claquettes) :

```php
foreach ($file as $client) {
    try {
        $videur->controler($client);
        echo "✅ {$client['nom']} entre" . PHP_EOL;
    } catch (TropJeuneException $e) {
        // traitement SPÉCIAL, placé AVANT le catch général
        echo "⛔ {$client['nom']} : {$e->getMessage()} — mais voici un ticket ado 🎟️" . PHP_EOL;
    } catch (RefusEntreeException $e) {
        // UN SEUL catch pour TenueNonReglementaire ET ListeNoire
        echo "⛔ {$client['nom']} : {$e->getMessage()}" . PHP_EOL;
    } finally {
        $videur->compter();
    }
}
```

**Question à traiter en commentaire** : pourquoi `catch (RefusEntreeException)` attrape-t-il aussi `ListeNoireException` ? Et que se passe-t-il si on **inverse** les deux `catch` ? (Réponse : un `catch` attrape le type demandé **et tous ses descendants** ; en inversant, le bloc `TropJeuneException` devient mort — Bob et Fatou ne reçoivent plus leur ticket.)

</details>

<details>
<summary><b>Bonus 2 — La machine à café complète</b> (exceptions porteuses de données, ordre des <code>catch</code>, <code>finally</code>)</summary>

**1. Trois exceptions personnalisées**, chacune avec un constructeur qui appelle `parent::__construct(<message>)` :

- `PlusDeCafeException` — message fixe ;
- `PlusDeGobeletException` — message fixe ;
- `MontantInsuffisantException` — `public function __construct(public readonly float $manque)` : transporte le montant manquant en plus du message.

**2. Une classe `MachineACafe`**

- `private const float PRIX = 0.50;` ;
- constructeur `public function __construct(private int $doses = 10, private int $gobelets = 5) {}` ;
- `commander(float $montantInsere): string` — **l'ordre des vérifications compte** : d'abord l'argent (le client peut corriger), puis les gobelets, puis les doses. Si tout est bon : décrémente les stocks, renvoie `"☕ Voici votre café ! (monnaie rendue : 0.50 €)"` ;
- `stocks(): string` — `"[stock : 8 dose(s), 2 gobelet(s)]"`.

**3. Une file de ~7 collègues** avec des montants variés (un radin à 0,20 €, un à qui il manque 5 centimes, un dernier pour qui il n'y aura plus de gobelet). Pour chacun :

```php
try {
    echo $machine->commander($montant) . PHP_EOL;
} catch (MontantInsuffisantException $e) {
    echo $e->getMessage() . " (manque : {$e->manque} €)" . PHP_EOL;
} catch (PlusDeGobeletException | PlusDeCafeException $e) {   // PHP 8 : plusieurs types
    echo $e->getMessage() . PHP_EOL;
} finally {
    echo $machine->stocks() . PHP_EOL;   // s'affiche à CHAQUE tour, succès ou échec
}
```

**À retenir** : la machine ne fait jamais planter le script ; une exception dédiée transporte des données (impossible avec `return false`) ; les `catch` vont du plus précis au plus général ; `finally` s'exécute toujours, même après un `return` dans le `try`.

</details>

<details>
<summary><b>Bonus 3 — La fusée à étages</b> (envelopper une exception, <code>previous</code>, niveaux d'abstraction)</summary>

**1. Deux exceptions** qui héritent de `RuntimeException` : `MoteurEnPanneException` et `DecollageAnnuleException`.

**2. Une classe `Moteur`**

- constructeur `private int $numero` ;
- `allumer(): void` — une fois sur deux (`random_int(0, 1)`), lève `MoteurEnPanneException` avec un message **technique** : `"Injecteur 2 bouché (pression : 0.3 bar au lieu de 4.2)"`. Sinon affiche `"✅ Moteur 2 allumé."`.

**3. Une classe `Fusee`**

- constructeur `public readonly string $nom`, `int $nbMoteurs = 3` → crée `$nbMoteurs` objets `Moteur` ;
- `decoller(): void` — dans un `try`, allume tous les moteurs. Si un `MoteurEnPanneException` survient, on **ne laisse pas fuiter le détail technique** : on le capture et on `throw` une `DecollageAnnuleException` avec un message **grand public** (`"Décollage annulé, restez calmes."`), en passant l'exception d'origine :

  ```php
  throw new DecollageAnnuleException('Décollage annulé, restez calmes.', previous: $e);
  ```

**4. Le script principal** attrape `DecollageAnnuleException` et affiche **deux** informations :

```php
echo '🚨 ' . $e->getMessage() . PHP_EOL;                       // pour le public
echo 'Cause technique : ' . $e->getPrevious()?->getMessage();  // pour les ingénieurs (nullsafe !)
```

**Bonus du bonus** : écrivez une boucle `while ($courante !== null)` qui remonte toute la chaîne des causes avec `getPrevious()` et l'affiche indentée.

**À relier au web** : c'est exactement ce qu'on fait avec une `PDOException` (`SQLSTATE[42S02] Table 'users' doesn't exist`) → l'utilisateur voit « Une erreur est survenue », le détail part dans les logs. Afficher le message SQL brut en production est **une faille de sécurité**.

</details>

<details>
<summary><b>Bonus 4 — Quiz « qui attrape quoi ? »</b></summary>

Pour chaque scénario : que s'affiche-t-il ? Écrivez votre réponse, puis vérifiez.

**Scénario 1**

```php
try {
    throw new RuntimeException('A');
} catch (Exception $e)      { echo '1'; }
catch (RuntimeException $e) { echo '2'; }
```

**Scénario 2**

```php
try {
    echo (int) '12 singes';
} catch (Exception $e) { echo 'attrapé'; }
finally               { echo ' / finally'; }
```

**Scénario 3**

```php
function boum(): never { throw new LogicException('B'); }

try {
    boum();
    echo 'après boum';
} catch (LogicException) {          // catch sans variable (PHP 8.0)
    echo 'attrapé B';
}
```

Notions testées : le **premier `catch` compatible gagne** (et `RuntimeException` hérite de `Exception`, donc le bloc `'2'` est mort) ; `(int) '12 singes'` vaut `12` **sans exception** ; `finally` s'exécute toujours ; le type de retour **`never`** (PHP 8.1) ; l'instruction après un `throw` est inaccessible ; le `catch` **sans variable** quand `$e` ne sert pas.

</details>

---

[⬅️ Retour à l'index des exercices](./README.md)
