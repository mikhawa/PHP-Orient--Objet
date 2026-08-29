# Corrigé 4.C — Cartographe de projet

## L'organisation proposée

```text
projet-jeu/
├── autoload.php
├── public/
│   └── index.php                     ← contrôleur frontal (pas de namespace)
└── App/
    ├── Model/
    │   ├── Hero.php                  → App\Model\Hero
    │   ├── Monstre.php               → App\Model\Monstre
    │   └── Objet.php                 → App\Model\Objet
    ├── Manager/
    │   ├── HeroManager.php           → App\Manager\HeroManager
    │   └── MonstreManager.php        → App\Manager\MonstreManager
    ├── Service/
    │   ├── Combat.php                → App\Service\Combat
    │   └── GenerateurDeDonjon.php    → App\Service\GenerateurDeDonjon
    └── Exception/
        ├── HeroMortException.php     → App\Exception\HeroMortException
        └── DonjonVideException.php   → App\Exception\DonjonVideException
```

## La justification en deux phrases

On regroupe les classes par **rôle technique** (modèles, accès aux données, services métier, exceptions) plutôt que par entité, parce qu'une classe se cherche d'abord par ce qu'elle *fait* : « où sont les managers ? » est une question plus fréquente que « où est tout ce qui concerne le héros ? ». La correspondance stricte namespace ↔ dossier permet à un autoloader PSR-4 de trouver n'importe quelle classe par simple transformation de son nom en chemin, sans configuration ni index.

## Les points qui comptent

**Un seul nom de dossier par segment de namespace.** `App\Manager\HeroManager` doit vivre dans `App/Manager/HeroManager.php`. Pas `app/manager/` (la casse compte sous Linux !), pas `App/Managers/` (pluriel), pas `App/Manager/Hero/Manager.php`.

**Un fichier = une classe = son nom exact.** `HeroManager.php` contient `class HeroManager` et rien d'autre. C'est ce qui rend l'autoload possible.

**Le contrôleur frontal n'a pas de namespace.** `public/index.php` n'est pas une classe, c'est un point d'entrée : il inclut l'autoloader puis utilise les classes. Il n'a donc rien à déclarer.

**Aucune classe dans le namespace global.** Cela évite tout risque de collision avec une bibliothèque tierce ou une future classe native de PHP. Le jour où PHP 8.x ajoute une classe `Combat` (peu probable, mais…), votre code continue de fonctionner.

## Variante acceptable : le découpage par domaine

Sur un projet plus gros, on regroupe parfois par **domaine métier** plutôt que par rôle :

```text
App/
├── Combat/          → Hero, Monstre, HeroManager, Combat, HeroMortException
└── Donjon/          → Donjon, GenerateurDeDonjon, DonjonVideException
```

**Avantage** : tout ce qui concerne le combat est au même endroit, on peut extraire un domaine entier sans le démonter.
**Inconvénient** : plus difficile à appréhender pour un débutant, et discutable tant que le projet reste petit.

Pour ce cours, gardez le découpage par rôle : c'est celui qu'utilisent la plupart des tutoriels et la structure historique de Symfony (`Entity/`, `Repository/`, `Controller/`, `Service/`).

## Ce que vous réutiliserez au chapitre 5

Ce plan est exactement celui que l'autoloader du chapitre 5 sait résoudre :

```php
// App\Manager\HeroManager
//   → str_replace('\\', '/', $class)
//   → 'App/Manager/HeroManager'
//   → RACINE . '/App/Manager/HeroManager.php'  ✅
```
