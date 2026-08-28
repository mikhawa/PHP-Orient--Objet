# Chapitre 6 — Les exceptions

> 📖 Cours : [6. Les exceptions](../README.md#6-les-exceptions)

L'exercice 6.A (☕ machine à café) se trouve [dans le cours, section 6.1](../README.md#61-exercice-ludique--la-machine-à-café). Faites-le d'abord ! Voici la suite.

---

## 🕶️ Exercice 6.B — Le videur de la boîte de nuit ⭐⭐

Le club « Le Namespace » ouvre ses portes. Vous êtes le videur, et vous travaillez avec une **hiérarchie d'exceptions**.

1. Créez la classe de base `RefusEntreeException extends Exception`, puis trois enfants :
    - `TropJeuneException` — le constructeur reçoit l'âge et fabrique le message `"18 ans minimum, reviens dans 3 ans !"` ;
    - `TenueNonReglementaireException` — reçoit l'objet du délit (`"claquettes-chaussettes"`…) ;
    - `ListeNoireException` — message fixe : `"Toi, on t'a reconnu. C'est NON."`
2. Créez la classe `Videur` avec la méthode `controler(array $client): void` qui lance la bonne exception selon le client (`['nom' => ..., 'age' => ..., 'tenue' => ..., 'blackliste' => bool]`) ou laisse passer.
3. Faites défiler une file de 6 clients variés. Le point clé : **un seul** bloc `catch (RefusEntreeException $e)` doit suffire à attraper les trois types (pourquoi ? c'est tout l'intérêt de la hiérarchie !) :

```text
✅ Aline entre dans le club 🎉
⛔ Bob refoulé : 18 ans minimum, reviens dans 3 ans !
⛔ Carla refoulée : Les claquettes-chaussettes ne passeront pas.
✅ Driss entre dans le club 🎉
⛔ Eddy refoulé : Toi, on t'a reconnu. C'est NON.
```

4. Ajoutez un `finally` qui incrémente un compteur `$personnesControlees` — refusées ou pas, tout le monde est contrôlé. Affichez le bilan final.

**Bonus** ⭐⭐⭐ : attrapez `TropJeuneException` **avant** le catch général pour offrir un traitement spécial (`"...mais tiens, un ticket pour la soirée ado de samedi 🎟️"`). Ordre des `catch` : du plus précis au plus général — inversez-les pour voir ce qui se passe.

---

## 🚀 Exercice 6.C — La fusée à étages (relancer une exception) ⭐⭐⭐

Une exception peut être attrapée, enrichie, puis **relancée** : c'est le paramètre `$previous` du constructeur.

1. Créez `MoteurEnPanneException` et `DecollageAnnuleException`.
2. La classe `Moteur` a une méthode `allumer(): void` qui lance `MoteurEnPanneException("Injecteur 3 bouché")` une fois sur deux (`random_int(0, 1)`).
3. La classe `Fusee` a une méthode `decoller(): void` qui appelle `allumer()` dans un `try...catch` et **relance** une exception de plus haut niveau :

```php
throw new DecollageAnnuleException(
    'Décollage annulé, restez calmes.',
    previous: $e   // on garde la cause technique !
);
```

4. Le script principal attrape `DecollageAnnuleException` et affiche les deux niveaux :

```text
🚨 Décollage annulé, restez calmes.
   Cause technique : Injecteur 3 bouché
```

(Indice : `$e->getPrevious()?->getMessage()` — tiens, un nullsafe !)

**Question de réflexion** : pourquoi le grand public (le script principal) reçoit-il un message différent de celui des ingénieurs (la cause technique) ? Où retrouve-t-on ce principe dans une vraie application web ?

---

## 🎯 Exercice 6.D — Quiz : qui attrape quoi ? ⭐

Prédisez l'affichage de chaque scénario avant d'exécuter :

```php
// --- Scénario 1 ---
try {
    throw new RuntimeException('A');
} catch (Exception $e) {
    echo '1';
} catch (RuntimeException $e) {
    echo '2';
}

// --- Scénario 2 ---
try {
    echo (int) '12 singes'; // pas d'exception ici… n'est-ce pas ?
} catch (Exception $e) {
    echo 'attrapé';
} finally {
    echo ' / finally';
}

// --- Scénario 3 ---
function boum(): never {
    throw new LogicException('B');
}
try {
    boum();
    echo 'après boum';
} catch (LogicException) {   // PHP 8 : catch sans variable !
    echo 'attrapé B';
}
```

---

[⬅️ Retour à l'index des exercices](./README.md)
