<?php
/**
 * Corrigé 3.C — Le menu de la cantine
 * Enum adossé sur int, tryFrom(), regroupement de cas dans un match.
 *
 * Utilisation : php 03c-cantine.php 3
 */

declare(strict_types=1);

enum Jour: int
{
    case Lundi = 1;
    case Mardi = 2;
    case Mercredi = 3;
    case Jeudi = 4;
    case Vendredi = 5;
    case Samedi = 6;
    case Dimanche = 7;

    public function menu(): string
    {
        return match ($this) {
            Jour::Lundi    => '🍟 Boulettes sauce lapin & frites',
            Jour::Mardi    => '🍝 Pâtes bolognaise (ou carbonara le mardi pair)',
            Jour::Mercredi => '🥘 Waterzooi de poulet',
            Jour::Jeudi    => '🐟 Poisson pané & purée',
            Jour::Vendredi => '🍕 Pizza du chef (le chef commande chez Luigi)',
            // Plusieurs cas partagent une même réponse : on les sépare par une virgule.
            Jour::Samedi, Jour::Dimanche => 'La cantine est fermée, débrouillez-vous',
        };
    }

    public function estOuvert(): bool
    {
        return match ($this) {
            Jour::Samedi, Jour::Dimanche => false,
            default => true,
        };
    }

    /** Bonus : le jour réel, via date('N') qui renvoie 1 (lundi) à 7 (dimanche). */
    public static function aujourdhui(): self
    {
        return self::from((int) date('N'));
    }
}

// ------------------------------------------------------- Lecture de l'argument

$numero = (int) ($argv[1] ?? 0);

// tryFrom() renvoie null au lieu de lever une ValueError : parfait pour
// valider une entrée utilisateur sans try/catch.
$jour = Jour::tryFrom($numero);

if ($jour === null) {
    echo "Le jour $numero n'existe pas dans ce plan de réalité 🌀" . PHP_EOL;
    echo "(essayez un nombre de 1 = lundi à 7 = dimanche)" . PHP_EOL;
} else {
    echo "{$jour->name} : {$jour->menu()}" . PHP_EOL;
}

// -------------------------------------------------------- Le menu de la semaine

echo PHP_EOL . "📋 MENU DE LA SEMAINE" . PHP_EOL;

foreach (Jour::cases() as $j) {
    printf(
        "  %s %-9s %s%s",
        $j->estOuvert() ? '🍽️ ' : '🔒',
        $j->name,
        $j->menu(),
        PHP_EOL
    );
}

// -------------------------------------------------------------- Le jour réel

$today = Jour::aujourdhui();
echo PHP_EOL . "📅 Aujourd'hui ({$today->name}) : {$today->menu()}" . PHP_EOL;

// ------------------------------------------------------- from() vs tryFrom()

echo PHP_EOL . <<<'TXT'
    ⚖️  from() OU tryFrom() ?

       ::from($v)     lève une ValueError si la valeur est inconnue.
                      → à utiliser quand une valeur invalide est un BUG
                        (ex. : une colonne SQL que vous contrôlez).

       ::tryFrom($v)  renvoie null si la valeur est inconnue.
                      → à utiliser quand une valeur invalide est NORMALE
                        (ex. : un paramètre d'URL, un formulaire, un argv).

       Règle simple : donnée interne → from(), donnée venue de l'extérieur
       → tryFrom() + un message clair pour l'utilisateur.
    TXT . PHP_EOL;
