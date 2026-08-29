<?php
/**
 * Corrigé 7.1 à 7.6 — match et les nouveautés PHP 8, étape par étape
 */

declare(strict_types=1);

// ═══════════════════════ 7.1 : switch → match ═══════════════════════

function emojiMeteo(string $temps): string
{
    return match ($temps) {
        'soleil' => '☀️',
        'pluie'  => '🌧️',
        'neige'  => '❄️',
        default  => '🤷',
    };
}

echo '── 7.1 ──' . PHP_EOL;
echo emojiMeteo('pluie') . PHP_EOL;

echo <<<'TXT'

   👉 RÉPONSES 7.1
      1. La version switch fait 13 lignes, la version match en fait 8.
      2. Aucun break, et un seul return au lieu de quatre.
         match est une EXPRESSION : elle produit une valeur.

TXT;

// ═══════════════════════ 7.2 : regrouper des cas ═══════════════════════

function typeDeJour(string $jour): string
{
    return match ($jour) {
        'samedi', 'dimanche' => 'week-end 🎉',
        default              => 'boulot 😅',
    };
}

echo '── 7.2 ──' . PHP_EOL;

foreach (['lundi', 'samedi', 'dimanche', 'jeudi'] as $jour) {
    echo '  ' . $jour . ' : ' . typeDeJour($jour) . PHP_EOL;
}

// ═══════════════════════ 7.3 : le filet de sécurité ═══════════════════════

function emojiMeteoStricte(string $temps): string
{
    return match ($temps) {      // pas de default !
        'soleil' => '☀️',
        'pluie'  => '🌧️',
        'neige'  => '❄️',
    };
}

echo PHP_EOL . '── 7.3 ──' . PHP_EOL;

try {
    echo emojiMeteoStricte('brouillard');
} catch (\UnhandledMatchError $e) {
    echo '  1. L\'erreur s\'appelle : UnhandledMatchError' . PHP_EOL;
    echo '     (' . $e->getMessage() . ')' . PHP_EOL;
}

echo <<<'TXT'
     2. Avec un switch sans default, il ne se serait RIEN passé :
        la fonction serait sortie sans rien renvoyer (null), et le bug
        aurait voyagé silencieusement jusqu'à l'affichage.

        Ce comportement de match est une FONCTIONNALITÉ, pas un défaut :
        il vous force à traiter les cas que vous ajoutez.

TXT;

// ═══════════════════════ 7.4 : match(true) ═══════════════════════

function mention(int $note): string
{
    // match(true) : on teste des CONDITIONS, pas des valeurs exactes.
    return match (true) {
        $note >= 16 => 'Excellent 🏆',
        $note >= 14 => 'Bien 😀',
        $note >= 10 => 'Passable 🙂',
        default     => 'À revoir 😬',
    };
}

echo '── 7.4 ──' . PHP_EOL;

foreach ([18, 15, 11, 4] as $note) {
    echo '  ' . $note . '/20 : ' . mention($note) . PHP_EOL;
}

// Démonstration du piège de l'ordre
function mentionCassee(int $note): string
{
    return match (true) {
        $note >= 10 => 'Passable 🙂',   // ← attrape TOUT dès 10
        $note >= 16 => 'Excellent 🏆',  // ← jamais atteint
        default     => 'À revoir 😬',
    };
}

echo PHP_EOL . '  ⚠️  Avec les conditions dans le mauvais ordre :' . PHP_EOL;
echo '     18/20 donne « ' . mentionCassee(18) . ' » au lieu d\'Excellent.' . PHP_EOL;
echo '     PHP prend la PREMIÈRE condition vraie : allez du plus strict' . PHP_EOL;
echo '     au plus large.' . PHP_EOL;

// ═══════════════════════ 7.5 : l'opérateur ?-> ═══════════════════════

class Ville
{
    public function __construct(public string $nom) {}
}

class Utilisateur
{
    public function __construct(
        public string $pseudo,
        public ?Ville $ville = null,    // ? = « Ville OU null »
    ) {}
}

$aline = new Utilisateur('Aline', new Ville('Charleroi'));
$bob = new Utilisateur('Bob');

echo PHP_EOL . '── 7.5 ──' . PHP_EOL;

// Sans ?-> : PHP émet un Warning et renvoie null
set_error_handler(function (int $no, string $msg): bool {
    echo '  ⚠️  Warning : ' . $msg . PHP_EOL;
    return true;
});
$test = $bob->ville->nom;
restore_error_handler();

// Avec ?-> et ?? : propre et sans surprise
echo '  ' . ($aline->ville?->nom ?? 'ville inconnue') . PHP_EOL;
echo '  ' . ($bob->ville?->nom ?? 'ville inconnue') . PHP_EOL;

// ═══════════════════════ 7.6 : les arguments nommés ═══════════════════════

class Pizza
{
    public function __construct(
        public string $pate = 'classique',
        public string $sauce = 'tomate',
        public bool $extraFromage = false,
        public bool $ananas = false,
    ) {}

    public function __toString(): string
    {
        return '🍕 pâte ' . $this->pate . ', sauce ' . $this->sauce
            . ($this->extraFromage ? ', extra fromage' : '')
            . ($this->ananas ? ', ananas 🍍 (assumé)' : '');
    }
}

echo PHP_EOL . '── 7.6 ──' . PHP_EOL;

// On ne précise QUE ce qui change, quelle que soit sa position
echo '  ' . new Pizza(ananas: true) . PHP_EOL;
echo '  ' . new Pizza(extraFromage: true, pate: 'fine') . PHP_EOL;

echo <<<'TXT'

   👉 RÉPONSE 7.6 : sans les arguments nommés, il aurait fallu écrire
      new Pizza('classique', 'tomate', false, true)
      c'est-à-dire répéter les trois valeurs par défaut — et les
      connaître par cœur. La moindre erreur d'ordre passe inaperçue.

TXT;
