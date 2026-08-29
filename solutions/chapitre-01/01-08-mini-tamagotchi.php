<?php
/**
 * Corrigé 1.8 — Le mini-Tamagotchi
 * (Version simple. Le Tamagotchi complet est dans 01a-tamagotchi.php)
 */

declare(strict_types=1);

class Tamagotchi
{
    private int $faim = 50;

    public function __construct(private string $nom) {}

    public function manger(): void
    {
        $this->faim = $this->faim - 20;
    }

    public function jouer(): void
    {
        $this->faim = $this->faim + 15;
    }

    public function etat(): string
    {
        return '🐣 ' . $this->nom . ' a une faim de ' . $this->faim . '/100';
    }
}

// ─────────────────────────── Programme de test ───────────────────────────

$pixel = new Tamagotchi('Pixel');
echo $pixel->etat() . PHP_EOL;

$pixel->manger();
echo $pixel->etat() . PHP_EOL;

$pixel->jouer();
echo $pixel->etat() . PHP_EOL;
