<?php
/**
 * Corrigé 3.1 à 3.4 — Le feu de signalisation, étape par étape
 */

declare(strict_types=1);

enum Feu
{
    case Rouge;
    case Orange;
    case Vert;

    // 3.2 : une méthode dans l'enum
    public function emoji(): string
    {
        return match ($this) {
            Feu::Rouge  => '🔴',
            Feu::Orange => '🟠',
            Feu::Vert   => '🟢',
        };
    }

    // 3.3 : une deuxième méthode, sur le même modèle
    public function duree(): int
    {
        return match ($this) {
            Feu::Rouge  => 25,
            Feu::Orange => 5,
            Feu::Vert   => 30,
        };
    }
}

// ─────────────────────────── 3.1 ───────────────────────────

$feu = Feu::Rouge;

echo $feu->name . PHP_EOL;

var_dump($feu === Feu::Rouge);   // true
var_dump($feu === Feu::Vert);    // false

// 👉 RÉPONSE 3.1 : la syntaxe Feu::Rouge est la même que pour une
//    CONSTANTE DE CLASSE (MaClasse::MA_CONSTANTE). C'est logique :
//    un cas d'enum est une valeur fixe, qu'on ne peut pas modifier.

// ─────────────────────────── 3.2 et 3.3 ───────────────────────────

echo PHP_EOL;
echo Feu::Vert->emoji() . PHP_EOL;     // 🟢
echo Feu::Orange->duree() . PHP_EOL;   // 5

// ─────────────────────────── 3.4 ───────────────────────────

echo PHP_EOL . '── Tous les cas ──' . PHP_EOL;

foreach (Feu::cases() as $etat) {
    echo $etat->emoji() . ' ' . $etat->name . ' : ' . $etat->duree() . ' s' . PHP_EOL;
}
