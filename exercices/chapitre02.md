# Chapitre 2 — L'héritage

> 📖 Cours : [2. L'héritage](../README.md#2-lhéritage)

Les exercices 2.A (🦁 ménagerie), 2.B (⚔️ tournoi), 2.C (🦸 super-pouvoirs en kit) et 2.D (🧮 compteur de population) se trouvent [dans le cours, section 2.11](../README.md#211-exercices-ludiques--série-2). Faites-les d'abord ! Voici la suite.

---

## 🦓 Exercice 2.E — Contrôle à l'entrée du zoo ⭐⭐

Le zoo de Charleroi vous confie sa sécurité.

1. Créez deux **interfaces** : `Dangereux` (méthode `niveauDeMenace(): int`, de 1 à 5) et `Caressable` (méthode `reactionAuxCalins(): string`).
2. Reprenez votre classe abstraite `Animal` (exercice 2.A) et créez :
    - `Lion` : `Dangereux` (menace 5), certainement pas caressable ;
    - `Lapin` : `Caressable` (« ronronne… non attendez, ça c'est le chat ») ;
    - `Ours` : `Dangereux` (menace 4) **ET** `Caressable` (« câlin accepté, survie non garantie ») — oui, une classe peut implémenter deux interfaces !
3. Écrivez la fonction `inspecterEnclos(array $animaux): void` qui parcourt le tableau et, grâce à `instanceof`, affiche pour chaque animal le bon panneau :

```text
🦁 Simba : ⚠️ DANGER niveau 5/5 — NE PAS APPROCHER
🐰 Paquerette : 🤗 caresses autorisées — « sautille de joie »
🐻 Baloo : ⚠️ DANGER niveau 4/5 — 🤗 caresses autorisées — « câlin accepté, survie non garantie »
```

4. Marquez chaque redéfinition avec `#[\Override]`. Glissez volontairement une faute de frappe dans un nom de méthode redéfinie et constatez que l'attribut vous sauve la mise.

**Bonus** ⭐⭐⭐ : la fonction `visiteEnfants(array $animaux): array` renvoie uniquement les animaux caressables ET non dangereux (utilisez `array_filter` avec une fonction fléchée).

---

## 👑 Exercice 2.F — La dynastie royale (parent::) ⭐⭐

1. Créez la classe `Monarque` avec la méthode `titreComplet(): string` qui renvoie `"Sa Majesté"`.
2. `Roi extends Monarque` redéfinit `titreComplet()` : elle appelle `parent::titreComplet()` et ajoute `" le Roi {$this->nom}"`.
3. `RoiSoleil extends Roi` redéfinit encore : `parent::titreComplet()` + `", lumière de la POO"`.
4. Instanciez un `RoiSoleil` nommé « Louis » et affichez son titre. Suivez mentalement la chaîne d'appels :

```text
Sa Majesté le Roi Louis, lumière de la POO
```

5. Déclarez maintenant `titreComplet()` **finale** dans `Roi`. Que se passe-t-il ? Pourquoi un développeur ferait-il ça ?

**Question de réflexion** : que se passerait-il si `RoiSoleil::titreComplet()` appelait `$this->titreComplet()` au lieu de `parent::titreComplet()` ? (Indice : ☠️ récursion infinie — expliquez pourquoi.)

---

## 🎭 Exercice 2.G — Quiz héritage : « Compilera, compilera pas ? » ⭐

Pour chaque extrait, prédisez : **erreur fatale** ou **ça fonctionne** (et qu'affiche-t-il) ?

```php
// --- Extrait 1 ---
abstract class A { abstract public function go(): string; }
$a = new A(); // ?

// --- Extrait 2 ---
class B { protected function secret(): string { return 'chut'; } }
class C extends B { public function espionner(): string { return $this->secret(); } }
echo (new C())->espionner(); // ?

// --- Extrait 3 ---
class D { public function hello(): string { return 'bonjour'; } }
class E extends D { protected function hello(): string { return 'salut'; } } // ?

// --- Extrait 4 ---
final class F {}
class G extends F {} // ?

// --- Extrait 5 ---
class H { private string $tresor = 'or'; }
class I extends H {
    public function voler(): string { return $this->tresor; }
}
echo (new I())->voler(); // ?
```

---

[⬅️ Retour à l'index des exercices](./README.md)
