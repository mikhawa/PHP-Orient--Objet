<?php
/**
 * Corrigé 2.E — Contrôle à l'entrée du zoo
 * Deux interfaces sur une même classe, instanceof, #[\Override].
 */

declare(strict_types=1);

interface Dangereux
{
    /** @return int de 1 à 5 */
    public function niveauDeMenace(): int;
}

interface Caressable
{
    public function reactionAuxCalins(): string;
}

abstract class Animal
{
    public function __construct(protected string $nom) {}

    abstract public function crier(): string;

    public function getNom(): string
    {
        return $this->nom;
    }

    public function sePresenter(): string
    {
        return "Je suis {$this->nom} et je fais : " . $this->crier();
    }
}

class Lion extends Animal implements Dangereux
{
    #[\Override]
    public function crier(): string
    {
        return 'ROAAAR !';
    }

    #[\Override]
    public function niveauDeMenace(): int
    {
        return 5;
    }
}

class Lapin extends Animal implements Caressable
{
    #[\Override]
    public function crier(): string
    {
        return '(bruit de museau qui frémit)';
    }

    #[\Override]
    public function reactionAuxCalins(): string
    {
        return 'sautille de joie';
    }
}

// Une classe PEUT implémenter plusieurs interfaces : c'est le contournement
// de l'absence d'héritage multiple.
class Ours extends Animal implements Dangereux, Caressable
{
    #[\Override]
    public function crier(): string
    {
        return 'GROUAH !';
    }

    #[\Override]
    public function niveauDeMenace(): int
    {
        return 4;
    }

    #[\Override]
    public function reactionAuxCalins(): string
    {
        return 'câlin accepté, survie non garantie';
    }
}

// ------------------------------------------------------------ L'inspection

/**
 * instanceof interroge le CONTRAT, pas la classe concrète.
 * On pourrait ajouter demain une classe Scorpion : cette fonction la gérerait
 * sans une ligne de modification, du moment qu'elle implémente Dangereux.
 */
function inspecterEnclos(array $animaux): void
{
    $emojis = ['Lion' => '🦁', 'Lapin' => '🐰', 'Ours' => '🐻'];

    foreach ($animaux as $animal) {
        $panneaux = [];

        if ($animal instanceof Dangereux) {
            $panneaux[] = sprintf(
                '⚠️ DANGER niveau %d/5 — NE PAS APPROCHER',
                $animal->niveauDeMenace()
            );
        }

        if ($animal instanceof Caressable) {
            $panneaux[] = '🤗 caresses autorisées — « '
                . $animal->reactionAuxCalins() . ' »';
        }

        if ($panneaux === []) {
            $panneaux[] = '😐 rien à signaler';
        }

        printf(
            '%s %s : %s%s',
            $emojis[$animal::class] ?? '🐾',
            $animal->getNom(),
            implode(' — ', $panneaux),
            PHP_EOL
        );
    }
}

$zoo = [
    new Lion('Simba'),
    new Lapin('Paquerette'),
    new Ours('Baloo'),
];

echo "🎫 INSPECTION DES ENCLOS" . PHP_EOL;
inspecterEnclos($zoo);

// ------------------------------------------- Bonus : la visite des enfants

/**
 * Caressable ET pas Dangereux : les seuls animaux fréquentables.
 */
function visiteEnfants(array $animaux): array
{
    return array_filter(
        $animaux,
        fn(Animal $a) => $a instanceof Caressable && !$a instanceof Dangereux
    );
}

echo PHP_EOL . "👶 Animaux autorisés pour la visite des enfants :" . PHP_EOL;
foreach (visiteEnfants($zoo) as $animal) {
    echo '  ✅ ' . $animal->getNom() . PHP_EOL;
}
echo '  (Baloo est écarté : il est Caressable, mais aussi Dangereux…)' . PHP_EOL;

// ------------------------------------------------ 4. Le filet de sécurité #[\Override]

echo PHP_EOL . <<<'TXT'
    🪤 L'EXPÉRIENCE À FAIRE : dans la classe Lion, renommez `crier` en `crierr`
       en gardant l'attribut #[\Override] au-dessus. PHP refuse de charger le
       fichier :

         Fatal error: Lion::crierr() has #[\Override] attribute, but no
         matching parent method exists

       Sans l'attribut, PHP aurait accepté silencieusement : `crier()` serait
       resté abstrait (erreur fatale plus loin) ou, dans un cas non abstrait,
       le parent aurait continué d'être appelé et vous auriez cherché le bug
       pendant une heure. L'attribut coûte 12 caractères et vous les rend au
       centuple. 🛡️
    TXT . PHP_EOL;
