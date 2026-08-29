# ✅ Corrigés des exercices

Un corrigé **exécutable** par exercice. Ce ne sont pas des extraits à recopier : chaque fichier tourne tel quel et affiche sa propre démonstration.

```bash
php solutions/chapitre-01/01-etapes-chat.php
```

## Deux familles de fichiers

| Nom du fichier | Correspond à |
|----------------|--------------|
| `XX-etapes-*.php` | Les **exercices numérotés** du chapitre (1.1, 1.2, 1.3…). C'est l'état final du fichier après toutes les étapes, avec un commentaire indiquant à quel exercice correspond chaque partie. |
| tous les autres | Les exercices du bloc **« Pour aller plus loin »**, plus ambitieux. |

Commencez toujours par le `XX-etapes-*.php` de votre chapitre.

> ⚠️ **À lire avant de regarder un corrigé.** Un corrigé lu avant d'avoir cherché ne vous apprend rien : votre cerveau reconnaît la solution sans savoir la produire. Cherchez d'abord, quitte à échouer — c'est l'échec qui grave la notion.

## Prérequis

| Besoin | Version | Concerne |
|--------|---------|----------|
| PHP | **8.1+** | la majorité des corrigés |
| PHP | **8.3+** | `#[\Override]`, constantes typées, `mb_str_pad` |
| PHP | **8.4+** | property hooks et visibilité asymétrique (1.E, projet final) |
| `pdo_sqlite` | — | chapitres 9 et 10 (aucun serveur MySQL requis) |

Vérifiez votre configuration :

```bash
php -v                      # version
php -m | grep pdo_sqlite    # extension SQLite
```

Les corrigés du chapitre 9 et le projet final utilisent **SQLite en mémoire** : rien à installer, rien à nettoyer. Chaque fichier indique en commentaire la ligne à changer pour passer à MySQL.

## Le catalogue

### [Chapitre 0 — Du procédural à l'objet](./chapitre-00/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| `00a-playlist.php` | 0.A | première classe, promotion de propriétés |
| `00b-chasse-aux-bugs.php` | 0.B | typage, méthode inexistante = erreur fatale |

### [Chapitre 1 — Classes et objets](./chapitre-01/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`01-etapes-chat.php`** | **1.1 à 1.7** | **classe, méthode, `$this`, constructeur, `private`, getter, setter** |
| **`01-08-mini-tamagotchi.php`** | **1.8** | **la synthèse du chapitre** |
| `01-08-bonus-borner.php` | 1.8 bonus | méthode privée, `__toString()` |
| `01a-tamagotchi.php` | 1.A | encapsulation, setters gardiens, `__toString()` |
| `01b-super-heros.php` | 1.B | arguments nommés, `readonly` |
| `01c-coffre-fort.php` | 1.C | état privé protégé, `hash_equals`, nowdoc |
| `01d-de-parlant.php` | 1.D | fabriques statiques, `random_int`, type `static` |
| `01e-jauge.php` | 1.E | **property hooks**, propriété virtuelle, `private(set)` |
| `01f-quiz.php` + `.md` | 1.F | référence vs `clone`, statique, propriétés dynamiques |

### [Chapitre 2 — Héritage](./chapitre-02/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`02-etapes-animaux.php`** | **2.1 à 2.8** | **`extends`, redéfinition, `parent::`, `abstract`, polymorphisme** |
| `02a-menagerie.php` | 2.A | classe abstraite, polymorphisme, `#[\Override]` |
| `02b-tournoi.php` | 2.B | interface, 1000 duels statistiques, équilibrage |
| `02c-traits.php` | 2.C | traits, conflits `insteadof`/`as` |
| `02d-recensement.php` | 2.D | statique, constante `final` typée |
| `02e-zoo.php` | 2.E | deux interfaces, `instanceof`, `array_filter` |
| `02f-dynastie.php` | 2.F | `parent::`, `final`, récursion infinie démontrée |
| `02g-quiz.php` + `.md` | 2.G | garde-fous de PHP + **la propriété fantôme** 👻 |

### [Chapitre 3 — Énumérations](./chapitre-03/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`03-etapes-feu.php`** | **3.1 à 3.4** | **enum pur, méthodes, `match`, `cases()`** |
| **`03-etapes-statut-pfc.php`** | **3.5 à 3.7** | **enum adossé, `from()`/`tryFrom()`, Pierre-Feuille-Ciseaux** |
| `03a-pierre-feuille-ciseaux.php` | 3.A | enum adossé, `match`, `from`/`tryFrom` |
| `03b-feu-signalisation.php` | 3.B | enum pur, `match` renvoyant un cas |
| `03c-cantine.php` | 3.C | enum `int`, regroupement de cas, `$argv` |
| `03d-chaines-magiques.php` | 3.D | avant/après : la fin des chaînes magiques |

### [Chapitre 4 — Espaces de noms](./chapitre-04/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| `guildes/` | 4.A | deux classes homonymes, alias `as`, constantes de namespace |
| `04b-piege-exception.php` | 4.B | **le piège n°1** : `\Exception` dans un namespace |
| `04c-cartographe-corrige.md` | 4.C | organiser un projet pour PSR-4 |

### [Chapitre 5 — Auto-chargement](./chapitre-05/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`etapes/`** | **5.1 à 5.5** | **`spl_autoload_register`, `__DIR__`, namespace → dossier** |
| `grimoire/` | bonus | version complète avec `App/Model/` et `App/Service/` |
| `05b-espion.php` | 5.B | **le chargement paresseux prouvé** |
| `05c-composer-corrige.md` | 5.C | ce que fait Composer sous le capot |

### [Chapitre 6 — Exceptions](./chapitre-06/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`06-etapes-exceptions.php`** | **6.1 à 6.5** | **`throw`, `try/catch`, exception personnalisée** |
| `06a-machine-a-cafe.php` | 6.A | exceptions porteuses de données, `finally` |
| `06b-videur.php` | 6.B | **hiérarchie d'exceptions**, ordre des `catch` |
| `06c-fusee.php` | 6.C | `previous`, niveaux d'abstraction, sécurité |
| `06d-quiz.php` + `.md` | 6.D | `never`, `catch` sans variable, `finally` et `return` |

### [Chapitre 7 — Match et nouveautés PHP 8](./chapitre-07/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`07-etapes-match.php`** | **7.1 à 7.6** | **`switch`→`match`, `match(true)`, `?->`, arguments nommés** |
| `07a-choixpeau.php` | 7.A | `match(true)`, `match` vs `switch`, `UnhandledMatchError` |
| `07b-pipeline.php` | 7.B | callables de 1re classe, chaînage fluide, opérateur pipe |
| `07c-gps.php` | 7.C | `?->`, **Warning ≠ Error** sur `null` |
| `07d-gateau.php` | 7.D | arguments nommés, `new` dans un initialiseur |

### [Chapitre 8 — Mapping SQL → classes](./chapitre-08/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`08-etapes-user.php`** | **8.1 à 8.6** | **ligne PDO → objet, validation, hydratation** |
| `08a-bibliotheque-interdite.php` | 8.A | setters validateurs, **`strip_tags` ≠ anti-XSS** |
| `08b-hydratation.php` | 8.B | hydratation décortiquée, `toArray()` inverse |
| `08c-paquet-de-cartes.php` | 8.C | enums + `readonly`, frontière SQL ↔ objet |

### [Chapitre 9 — Managers PDO](./chapitre-09/)
| Fichier | Exercice | Notions clés |
|---------|----------|--------------|
| **`09-etapes-manager.php`** | **9.1 à 9.6** | **CRUD complet, `lastInsertId()`, injection SQL** |
| `09a-gardien-de-creatures.php` | 9.A | CRUD complet, `lastInsertId`, `GROUP BY` |
| `09b-borne-arcade.php` | 9.B | `LIMIT` paramétré, **injection SQL démontrée** 💣 |
| `09c-transaction-marchand.php` | 9.C | `beginTransaction`/`commit`/`rollBack` |
| `09d-manager-generique.php` | 9.D | manager abstrait, instanciation dynamique, liste blanche |

### [Chapitre 10 — Projet final](./chapitre-10/)
| Fichier | Correspond à |
|---------|--------------|
| **`10-etapes-arene.php`** | **les 6 étapes du projet** — un seul fichier, à lire en premier |
| `arene/` | la version avancée : namespaces, autoload, transaction |

```bash
php solutions/chapitre-10/10-etapes-arene.php     # la version des 6 étapes
cd solutions/chapitre-10/arene && php public/index.php   # la version avancée
```

```bash
cd solutions/chapitre-10/arene && php public/index.php
```

Structure du projet, à reproduire pour vos propres travaux :

```text
arene/
├── autoload.php                    ← le seul require du projet
├── App/
│   ├── Enum/ClasseHero.php         ← enum adossé + comportements
│   ├── Exception/                  ← 2 exceptions métier
│   ├── Model/
│   │   ├── AbstractModel.php       ← hydratation
│   │   ├── Combattant.php          ← l'interface (le contrat)
│   │   ├── Hero.php                ← classe abstraite
│   │   ├── Guerrier.php            ← coup critique 10 %
│   │   ├── Mage.php                ← boule de feu, 1 tour sur 2
│   │   └── Voleur.php              ← esquive 25 %
│   ├── Manager/HeroManager.php     ← CRUD + classement + transaction
│   └── Service/Arene.php           ← la logique de combat
└── public/index.php                ← contrôleur frontal
```

## Les corrigés à ne pas manquer

Même si vous avez réussi l'exercice, cinq corrigés apportent quelque chose que l'énoncé ne demandait pas :

1. **`02g-quiz.php`** — la propriété fantôme : un enfant qui écrit dans une propriété `private` du parent crée une copie invisible. Les deux coexistent. C'est le bug parfait, et la raison d'être de la dépréciation en PHP 8.2.
2. **`07c-gps.php`** — lire une propriété sur `null` est un **Warning** (le script continue avec `null`), alors qu'appeler une **méthode** sur `null` est une **Error** fatale. La confusion est très répandue.
3. **`08a-bibliotheque-interdite.php`** — `strip_tags()` retire les balises mais **garde le texte** : ce n'est pas une protection XSS. On échappe à l'affichage, avec `htmlspecialchars()`.
4. **`09b-borne-arcade.php`** — l'injection SQL exécutée pour de vrai : 0 résultat avec une requête préparée, **toute la table** avec une concaténation.
5. **`02b-tournoi.php`** — la poule perd toujours, contrairement à l'intuition. Le corrigé fait le calcul et vous invite à rééquilibrer : c'est du game design.

## Une remarque sur le style

Ces corrigés sont **volumineux et très commentés** : ils servent de support de cours, pas de modèle de concision. Dans du vrai code, on ne commente pas chaque ligne — on écrit du code assez clair pour s'en passer, et on réserve les commentaires au *pourquoi*, jamais au *quoi*.

Notez aussi qu'ils affichent tout dans le terminal (`echo`, `printf`). Dans une application web, ce travail revient aux **vues** : les modèles et les managers ne devraient rien afficher du tout.
