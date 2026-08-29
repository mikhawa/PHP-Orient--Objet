# Corrigé 1.F — Quiz « Que va afficher ce code ? »

Lancez `php 01f-quiz.php` pour vérifier chaque réponse en direct.

---

### Extrait 1 → `trésor`

```php
$a = new Boite();
$b = $a;              // ⚠️ on copie l'ÉTIQUETTE, pas l'objet
$b->contenu = 'trésor';
echo $a->contenu;     // trésor
```

**Pourquoi** : les objets sont manipulés **par référence**. `$a` et `$b` désignent le même objet en mémoire. Modifier par `$b` change ce que voit `$a`. C'est le piège n°1 des débutants en POO — et la différence majeure avec les tableaux, qui sont copiés par valeur.

---

### Extrait 2 → `trésor` (inchangé)

```php
$c = clone $a;          // clone crée un VRAI nouvel objet
$c->contenu = 'chaussette';
echo $a->contenu;       // trésor
```

**Pourquoi** : `clone` duplique l'objet. `$c` est indépendant. ⚠️ Attention, `clone` fait une copie **de surface** : si une propriété contient elle-même un objet, les deux copies partageront cet objet imbriqué (il faut alors écrire `__clone()`).

---

### Extrait 3 → `Error: Cannot modify readonly property Portefeuille::$solde`

**Pourquoi** : une propriété `readonly` (PHP 8.1) n'accepte **qu'une seule initialisation**, et uniquement depuis l'intérieur de la classe. Ni l'extérieur, ni un setter, ni même le constructeur une deuxième fois ne peuvent la modifier. C'est une erreur fatale, pas un avertissement.

---

### Extrait 4 → `3`

**Pourquoi** : `$tics` est **statique**, donc partagée par la classe entière, pas par instance. `$c1` et `$c2` incrémentent le même compteur : 1 + 2 = 3. Notez qu'on peut appeler une méthode statique via `self::` depuis une méthode d'instance, mais l'inverse est impossible (pas de `$this` dans une méthode statique).

---

### Extrait 5 → `Deprecated: Creation of dynamic property Fantome::$boo is deprecated`

**Pourquoi** : depuis **PHP 8.2**, créer une propriété non déclarée est **déprécié**. Le code fonctionne encore (la propriété est bien créée), mais PHP vous prévient que ça deviendra une erreur. Pour l'autoriser explicitement : `#[\AllowDynamicProperties]` au-dessus de la classe. La bonne pratique reste de **déclarer toutes ses propriétés**.

---

## Barème

| Score | Verdict |
|-------|---------|
| 5/5 | 🧙 Architecte objet |
| 3–4 | ⚔️ Apprenti solide |
| 0–2 | 🐣 Relisez le chapitre 1, il ne vous en voudra pas |

Les deux pièges les plus fréquents : l'extrait 1 (référence vs copie) et l'extrait 4 (statique partagé). Si vous les avez ratés, ce sont exactement les deux notions à revoir en priorité.
