<?php
// Le namespace est la TOUTE PREMIÈRE instruction du fichier.
// Il correspond au chemin du dossier : guildes/Mages → Guildes\Mages
namespace Guildes\Mages;

class Potion
{
    public function __construct(
        public readonly string $nom,
        public readonly int $mana,
    ) {}

    public function boire(): string
    {
        return "✨ +{$this->mana} mana ! Vous scintillez légèrement.";
    }
}

// Bonus : une constante DANS un namespace (hors classe).
const DEVISE = 'La connaissance avant la puissance.';
