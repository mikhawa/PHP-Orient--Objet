# Corrigé 2.G — Quiz « Compilera, compilera pas ? »

Lancez `php 02g-quiz.php` pour voir chaque cas s'exécuter isolément.

---

### Extrait 1 → ❌ **Erreur fatale**

```
Error: Cannot instantiate abstract class A
```

Une classe abstraite est un **modèle incomplet**. Elle décrit ce que ses enfants devront savoir faire (`go()`), sans le faire elle-même. L'instancier n'aurait aucun sens : que se passerait-il si on appelait `$a->go()` ?

---

### Extrait 2 → ✅ **Fonctionne**, affiche `chut`

`secret()` est `protected` : inaccessible depuis l'extérieur, mais **parfaitement accessible depuis la classe enfant**. `C` peut donc l'appeler depuis sa propre méthode `espionner()`, qui est publique. C'est exactement l'usage prévu de `protected` : partager avec sa descendance sans ouvrir au monde entier.

---

### Extrait 3 → ❌ **Erreur fatale**

```
Fatal error: Access level to E::hello() must be public (as in class D)
```

On peut **élargir** une visibilité en redéfinissant (`protected` → `public`), jamais la **réduire** (`public` → `protected`). Sinon, ce code casserait :

```php
function saluer(D $objet) { echo $objet->hello(); } // promis public par D
saluer(new E()); // mais E l'a rendue protected → contrat rompu
```

C'est le **principe de substitution de Liskov** : un enfant doit pouvoir remplacer son parent partout, sans surprise.

---

### Extrait 4 → ❌ **Erreur fatale**

```
Fatal error: Class G cannot extend final class F
```

`final` sur une classe interdit tout héritage. C'est le but.

---

### Extrait 5 → ❌ **Warning puis erreur fatale** (et c'est plus subtil qu'il n'y paraît)

```
Warning: Undefined property: I::$tresor
Fatal error: Uncaught TypeError: I::voler(): Return value must be of type
             string, null returned
```

⚠️ **Attention au piège** : le message n'est *pas* « Cannot access private property ». Depuis la classe enfant, la propriété privée du parent est tout simplement **invisible** — comme si elle n'existait pas. `$this->tresor` vaut `null`, d'où le `TypeError` sur le type de retour `string`.

Nuance à bien saisir :

| Où ? | Résultat |
|------|----------|
| Depuis l'**enfant** (`$this->tresor`) | `Warning: Undefined property` → `null` |
| Depuis l'**extérieur** (`$objet->tresor`) | `Error: Cannot access private property` |

Et le vrai danger est en **écriture** : `$this->tresor = 'volé';` depuis l'enfant ne modifie pas la propriété du parent — cela crée une **propriété dynamique distincte** portant le même nom ! Le parent garde `'or'`, l'enfant manipule sa propre copie fantôme, et le bug est indétectable à l'œil nu.

C'est précisément pour ça que PHP 8.2 a déprécié les propriétés dynamiques : cette classe de bugs disparaît. Testez-le :

```php
class H { private string $tresor = 'or'; public function voir(): string { return $this->tresor; } }
class I extends H {
    public function voler(): void { $this->tresor = 'volé'; } // Deprecated (PHP 8.2+)
}
$i = new I();
$i->voler();
echo $i->voir(); // "or" — le parent n'a rien vu passer !
```

**Pour qu'un enfant accède réellement à la donnée** : passez la propriété en `protected`, ou fournissez un getter/setter dans le parent.

---

## Récapitulatif

| Extrait | Verdict | Notion |
|---------|---------|--------|
| 1 | ❌ fatale | classe abstraite non instanciable |
| 2 | ✅ `chut` | `protected` accessible dans l'enfant |
| 3 | ❌ fatale | on n'ampute jamais une visibilité |
| 4 | ❌ fatale | `final` bloque l'héritage |
| 5 | ❌ Warning + fatale | `private` est *invisible* depuis l'enfant (≠ inaccessible) |
