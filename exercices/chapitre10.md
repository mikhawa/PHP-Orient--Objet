# Chapitre 10 — Projet final et quêtes annexes

> 📖 Cours : [10. Mini-projet final — L'Arène des Héros](../README.md#10-mini-projet-final--larène-des-héros)

Le projet principal (⚔️ **L'Arène des Héros**) est décrit en détail [dans le cours, section 10](../README.md#10-mini-projet-final--larène-des-héros), avec son cahier des charges et son barème. C'est **le** livrable du module.

Ci-dessous : des **quêtes annexes** pour enrichir l'Arène une fois le socle terminé, et deux projets alternatifs pour celles et ceux qui préfèrent un autre thème.

---

## 🗡️ Quêtes annexes pour l'Arène

À piocher selon votre appétit — chacune réinvestit un chapitre précis.

### Quête 1 — L'armurerie ⭐⭐ (chapitres 2 et 3)

Ajoutez un enum `TypeArme: string` (`Epee`, `Baton`, `Dague`, `PoulePneumatique`) avec `degatsBonus(): int` et `emoji(): string`. Un héros peut **équiper** une arme (propriété `?TypeArme`), ce qui modifie ses dégâts. Table `hero` : ajoutez une colonne `arme VARCHAR(30) NULL`, et le setter convertit avec `TypeArme::tryFrom()`.

### Quête 2 — Le journal de combat ⭐⭐ (chapitre 2, traits)

Créez un trait `Journalisable` avec une propriété `$journal` (tableau) et les méthodes `noter(string $ligne): void` et `afficherJournal(): void`. Utilisez-le dans `Arene` **et** dans `HeroManager` (deux classes sans lien d'héritage — c'est tout l'intérêt du trait). À la fin du tournoi, exportez le journal dans un fichier `combats.log`.

### Quête 3 — Les effets de statut ⭐⭐⭐ (chapitres 2, 3 et 7)

Enum `Statut: string` : `Empoisonne` (perd 5 PV par tour), `Beni` (+10 % dégâts), `Petrifie` (passe son tour), `Normal`. Un héros porte un statut ; à chaque tour, `Arene` applique son effet avec un `match`. Un `Mage` peut infliger un statut au lieu d'attaquer, une fois par combat.

### Quête 4 — Le mode spectateur ⭐⭐ (chapitre 1)

Ajoutez à `Hero` une propriété `public private(set) int $degatsInfligesTotal` (visibilité asymétrique, PHP 8.4) mise à jour à chaque attaque, et une propriété virtuelle `public float $degatsMoyens { get => ... }`. À la fin du tournoi, affichez les statistiques de chaque héros — impossible pour un tricheur de les modifier de l'extérieur.

### Quête 5 — La persistance complète ⭐⭐⭐ (chapitre 9)

Créez une table `combat` (`id`, `hero_a_id`, `hero_b_id`, `vainqueur_id`, `nb_tours`, `joue_le`) et un `CombatManager`. Chaque combat est enregistré dans une **transaction** avec la mise à jour des victoires/défaites : soit tout est sauvegardé, soit rien. Ajoutez `historique(int $heroId): array` — le palmarès d'un héros.

### Quête 6 — L'interface web ⭐⭐⭐ (chapitres 4, 5 et 9)

Transformez le script en mini-application MVC : `public/index.php` en contrôleur frontal, un formulaire HTML pour créer son héros, une page classement, une page « lancer un combat ». Rien de neuf côté POO — tout est déjà en place grâce aux namespaces, à l'autoload et aux managers. C'est la démonstration que votre architecture était bonne. 🏗️

---

## 🎯 Projet alternatif A — Le Gestionnaire de Potions 🧪 ⭐⭐⭐

Même exigences techniques que l'Arène, thème différent.

**Le pitch** : vous tenez l'apothicairerie du village. Les clients commandent des potions, vous gérez les stocks d'ingrédients et le grimoire de recettes.

**À produire** :

| Élément | Ce que ça exerce |
|---------|------------------|
| enum `Effet: string` (Soin, Force, Invisibilité, Chance) avec `description()` et `prixBase()` | chapitre 3 |
| interface `Consommable` (`consommer(): string`, `estPerime(): bool`) | chapitre 2 |
| classe abstraite `ProduitAlchimique` + enfants `Potion`, `Elixir` (effet doublé), `Philtre` (effet aléatoire !) | chapitre 2 |
| trait `Datable` (`creeLe`, `perimeLe`, `joursRestants()`) | chapitre 2 |
| exceptions `IngredientManquantException`, `RecetteInconnueException`, `PotionPerimeeException` | chapitre 6 |
| tables `potion`, `ingredient`, `recette` + les managers associés | chapitres 8 et 9 |
| `Chaudron::preparer(string $recette): Potion` — vérifie les stocks, les décrémente **en transaction**, échoue proprement | chapitres 6 et 9 |

**Le petit plus rigolo** : 5 % de chance que la préparation rate et produise une `PotionRatée` aux effets imprévisibles (`match(random_int(1, 4))`). 🤢

---

## 🎯 Projet alternatif B — La Médiathèque du CF2M 📚 ⭐⭐

Plus proche d'un vrai projet professionnel, moins fantastique — parfait pour un portfolio.

**À produire** :

- enum `TypeMedia: string` (Livre, DVD, JeuVideo, Vinyle) avec `dureeEmpruntJours(): int` (14, 7, 21, 7) ;
- classe abstraite `Media` (hydratation) + trois enfants avec des propriétés spécifiques (`nbPages`, `duree`, `plateforme`) ;
- interface `Empruntable` (`estDisponible()`, `emprunter(Membre $m)`, `rendre()`) ;
- `Membre` avec une limite de 3 emprunts simultanés → `TropDEmpruntsException` ;
- calcul des **retards** avec `DateTimeImmutable` et une amende de 0,20 €/jour → `MediaEnRetardException` ;
- tables `media`, `membre`, `emprunt` + managers, dont `getEnRetard(): array` (une jointure SQL !) ;
- un rapport final : les 3 médias les plus empruntés, le membre le plus assidu, le total des amendes. 💸

---

## ✅ Checklist de rendu (tous projets confondus)

Avant de dire « c'est fini », vérifiez :

- [ ] Aucun `require` en dehors de l'autoloader
- [ ] Toutes les classes ont un namespace correspondant à leur dossier
- [ ] Toutes les propriétés sont **typées** et **privées ou protégées** (sauf `readonly` public assumé)
- [ ] Aucune propriété dynamique créée à la volée
- [ ] Au moins un enum, une interface, une classe abstraite, un trait
- [ ] Toutes les redéfinitions portent `#[\Override]`
- [ ] Au moins deux exceptions personnalisées, réellement attrapées quelque part
- [ ] **Zéro** variable concaténée dans une requête SQL — que des requêtes préparées
- [ ] Le script tourne sans `Warning` ni `Notice` (testez avec `error_reporting(E_ALL)`)
- [ ] Un `README.md` explique comment installer la base et lancer le projet
- [ ] Ça vous a fait sourire au moins une fois 😄

---

[⬅️ Retour à l'index des exercices](./README.md)
