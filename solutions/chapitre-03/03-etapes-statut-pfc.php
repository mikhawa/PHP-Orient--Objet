<?php
/**
 * Corrigé 3.5 à 3.7 — L'enum adossé, from/tryFrom, Pierre-Feuille-Ciseaux
 */

declare(strict_types=1);

// ═══════════════════════ 3.5 : l'enum ADOSSÉ ═══════════════════════

enum Statut: string          // ": string" = chaque cas a une valeur texte
{
    case Brouillon = 'brouillon';
    case Publie = 'publie';
    case Archive = 'archive';

    public function libelle(): string
    {
        return match ($this) {
            Statut::Brouillon => '📝 En cours de rédaction',
            Statut::Publie    => '✅ En ligne',
            Statut::Archive   => '📦 Archivé',
        };
    }
}

echo '── 3.5 : l\'enum adossé ──' . PHP_EOL;

// Vers la base de données : on stocke ->value
echo 'Statut::Publie->value = ' . Statut::Publie->value . PHP_EOL;

// Depuis la base de données : on reconstruit l'enum
$statut = Statut::from('brouillon');
echo 'Statut::from(\'brouillon\')->name = ' . $statut->name . PHP_EOL;

echo PHP_EOL . 'Tous les libellés :' . PHP_EOL;
foreach (Statut::cases() as $s) {
    echo '  ' . $s->libelle() . PHP_EOL;
}

// ═══════════════════════ 3.6 : from() ou tryFrom() ? ═══════════════════════

echo PHP_EOL . '── 3.6 : quand la valeur n\'existe pas ──' . PHP_EOL;

// from() ARRÊTE le programme (on l'attrape ici pour pouvoir continuer)
try {
    Statut::from('publiée');
} catch (ValueError $e) {
    echo '1. from(\'publiée\')    → ValueError : le programme s\'arrête' . PHP_EOL;
}

// tryFrom() renvoie null, tranquillement
$b = Statut::tryFrom('publiée');
echo '2. tryFrom(\'publiée\') → ';
var_dump($b);

echo <<<'TXT'

   👉 RÉPONSE 3 : pour un texte tapé par un utilisateur, on utilise
      tryFrom(). Une faute de frappe dans un formulaire est NORMALE :
      elle mérite un message poli, pas un plantage.

      On garde from() pour les données qu'on contrôle soi-même
      (une colonne de sa propre base) : là, une valeur inconnue est
      un vrai bug, et il vaut mieux qu'il se voie.

TXT;

// Exemple concret de bon usage
$saisie = 'publiée';
$statutChoisi = Statut::tryFrom($saisie);

echo $statutChoisi === null
    ? "   Exemple : « $saisie » n'est pas un statut valide. Choisissez parmi : "
        . implode(', ', array_column(Statut::cases(), 'value')) . PHP_EOL
    : "   Statut retenu : {$statutChoisi->libelle()}" . PHP_EOL;

// ═══════════════════════ 3.7 : Pierre-Feuille-Ciseaux ═══════════════════════

enum Coup: string
{
    case Pierre = 'pierre';
    case Feuille = 'feuille';
    case Ciseaux = 'ciseaux';

    public function emoji(): string
    {
        return match ($this) {
            Coup::Pierre  => '✊',
            Coup::Feuille => '✋',
            Coup::Ciseaux => '✌️',
        };
    }

    public function bat(Coup $autre): bool
    {
        return match ($this) {
            Coup::Pierre  => $autre === Coup::Ciseaux,
            Coup::Feuille => $autre === Coup::Pierre,
            Coup::Ciseaux => $autre === Coup::Feuille,
        };
    }
}

echo PHP_EOL . '── 3.7 : Pierre-Feuille-Ciseaux ──' . PHP_EOL;

var_dump(Coup::Pierre->bat(Coup::Ciseaux));   // true
var_dump(Coup::Pierre->bat(Coup::Feuille));   // false

echo PHP_EOL . 'Toutes les combinaisons :' . PHP_EOL;

foreach (Coup::cases() as $a) {
    foreach (Coup::cases() as $b) {
        $resultat = match (true) {
            $a === $b       => 'égalité',
            $a->bat($b)     => 'gagne',
            default         => 'perd',
        };

        echo '  ' . $a->emoji() . ' contre ' . $b->emoji() . ' : ' . $resultat . PHP_EOL;
    }
}
