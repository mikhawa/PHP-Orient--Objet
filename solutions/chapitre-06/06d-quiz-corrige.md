# Corrigé 6.D — Quiz « Qui attrape quoi ? »

Lancez `php 06d-quiz.php` pour voir chaque scénario s'exécuter.

---

### Scénario 1 → affiche `1`

```php
try {
    throw new RuntimeException('A');
} catch (Exception $e)        { echo '1'; }   // ← c'est celui-ci
catch (RuntimeException $e)   { echo '2'; }   // ← jamais atteint
```

**Pourquoi** : `RuntimeException` hérite de `Exception`. Le **premier catch compatible gagne**, et PHP les examine dans l'ordre d'écriture. Le second bloc est **mort** : aucun chemin d'exécution ne peut l'atteindre.

**La leçon** : ordonnez toujours vos `catch` du **plus précis au plus général**. PHP ne vous préviendra pas ; PHPStan et Psalm, si.

---

### Scénario 2 → affiche `12 / finally`

```php
try {
    echo (int) '12 singes';   // vaut 12, pas d'exception
} catch (Exception $e) { echo 'attrapé'; }
finally               { echo ' / finally'; }
```

**Pourquoi** : `(int) '12 singes'` vaut `12` en PHP — le cast s'arrête au premier caractère non numérique, **sans lever d'exception** (un `Warning` est émis en PHP 8 pour les chaînes non numériques *à l'usage arithmétique*, mais un cast explicite reste silencieux). Le `catch` ne se déclenche donc pas.

Et `finally` **s'exécute toujours** : exception ou pas, `return` ou pas. C'est bien tout l'intérêt du bloc.

---

### Scénario 3 → affiche `attrapé B` (et **pas** `après boum`)

```php
function boum(): never { throw new LogicException('B'); }

try {
    boum();
    echo 'après boum';     // ← INACCESSIBLE
} catch (LogicException) {  // PHP 8 : catch sans variable
    echo 'attrapé B';
}
```

Trois notions en trois lignes :

**Le type de retour `never`** (PHP 8.1) déclare que la fonction ne rend **jamais** la main : elle lève une exception ou appelle `exit`. Les analyseurs statiques savent alors que le code qui suit son appel est inaccessible.

**L'instruction après un `throw` n'est jamais exécutée** — l'exception interrompt le flux immédiatement et remonte jusqu'au `catch`.

**Le `catch` sans variable** (PHP 8.0) : quand le message ne vous intéresse pas, `catch (LogicException)` suffit. Plus besoin d'un `$e` inutilisé qui fait râler les linters.

---

## Récapitulatif

| Scénario | Affiche | Notion |
|----------|---------|--------|
| 1 | `1` | premier catch compatible gagne → du précis au général |
| 2 | `12 / finally` | pas d'exception ; `finally` s'exécute toujours |
| 3 | `attrapé B` | `never`, flux interrompu, catch sans variable |
