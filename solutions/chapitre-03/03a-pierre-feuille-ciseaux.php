<?php
/**
 * Corrigé 3.A — Pierre, Feuille, Ciseaux
 * Enum adossé, méthodes dans un enum, match, méthode statique.
 */

declare(strict_types=1);

enum Coup: string
{
    case Pierre = 'pierre';
    case Feuille = 'feuille';
    case Ciseaux = 'ciseaux';

    /** Le cœur du jeu, en trois lignes lisibles. */
    public function bat(Coup $autre): bool
    {
        return match ($this) {
            Coup::Pierre  => $autre === Coup::Ciseaux,
            Coup::Feuille => $autre === Coup::Pierre,
            Coup::Ciseaux => $autre === Coup::Feuille,
        };
        // Notez : pas de `default`. Si demain on ajoute un case sans traiter
        // ce match, PHP lève une UnhandledMatchError au lieu de renvoyer
        // silencieusement une mauvaise réponse. L'oubli devient impossible.
    }

    public function emoji(): string
    {
        return match ($this) {
            Coup::Pierre  => '✊',
            Coup::Feuille => '✋',
            Coup::Ciseaux => '✌️',
        };
    }

    /** Le coup de l'ordinateur. cases() renvoie tous les cas de l'enum. */
    public static function aleatoire(): self
    {
        $tous = self::cases();
        return $tous[array_rand($tous)];
    }
}

class Partie
{
    private int $scoreJoueur = 0;
    private int $scoreOrdinateur = 0;

    public function __construct(private int $manches = 5) {}

    public function jouer(): void
    {
        echo "✊✋✌️  PIERRE - FEUILLE - CISEAUX — {$this->manches} manches" . PHP_EOL . PHP_EOL;

        for ($i = 1; $i <= $this->manches; $i++) {
            $joueur = Coup::aleatoire();      // en vrai : lecture au clavier
            $ordinateur = Coup::aleatoire();

            $verdict = match (true) {
                $joueur === $ordinateur      => 'égalité…',
                $joueur->bat($ordinateur)    => 'vous gagnez !',
                default                      => 'l\'ordinateur gagne !',
            };

            if ($joueur->bat($ordinateur)) {
                $this->scoreJoueur++;
            } elseif ($ordinateur->bat($joueur)) {
                $this->scoreOrdinateur++;
            }

            printf(
                "Manche %d : Vous %s vs 🤖 %s → %s%s",
                $i, $joueur->emoji(), $ordinateur->emoji(), $verdict, PHP_EOL
            );
        }

        $this->afficherScore();
    }

    private function afficherScore(): void
    {
        $conclusion = match (true) {
            $this->scoreJoueur > $this->scoreOrdinateur => 'GG ! 🎉',
            $this->scoreJoueur < $this->scoreOrdinateur => 'La machine a gagné… 🤖',
            default => 'Match nul, revanche ? 🤝',
        };

        printf(
            "%s🏆 Score final : Vous %d — %d Ordinateur. %s%s",
            PHP_EOL, $this->scoreJoueur, $this->scoreOrdinateur, $conclusion, PHP_EOL
        );
    }
}

(new Partie())->jouer();

// ------------------------------------------------- Ce que l'enum apporte

echo PHP_EOL . "🔒 L'enum rend les erreurs IMPOSSIBLES :" . PHP_EOL;

// Conversion depuis une chaîne (ex. : une saisie utilisateur, une colonne SQL)
$saisie = 'ciseaux';
$coup = Coup::from($saisie);
echo "  Coup::from('$saisie') → {$coup->name} {$coup->emoji()}" . PHP_EOL;

try {
    Coup::from('cisaux'); // faute de frappe
} catch (ValueError $e) {
    echo "  Coup::from('cisaux') → ValueError : la faute de frappe est détectée !" . PHP_EOL;
}

// tryFrom() : la version tolérante, renvoie null au lieu de lever une exception
var_dump(Coup::tryFrom('cisaux'));

echo PHP_EOL . <<<'TXT'
    🖖 BONUS LEZARD-SPOCK : ajoutez les cases Lezard et Spock, puis complétez
       le match de bat() avec les tableaux de victoires :
         Pierre  bat Ciseaux et Lezard
         Feuille bat Pierre  et Spock
         Ciseaux bat Feuille et Lezard
         Lezard  bat Feuille et Spock
         Spock   bat Pierre  et Ciseaux
       Utilisez in_array($autre, [...], strict: true) dans chaque branche.
       Le reste du programme (Partie, scores, affichage) ne change PAS d'une
       ligne : c'est le signe d'une bonne modélisation. ✅
    TXT . PHP_EOL;
