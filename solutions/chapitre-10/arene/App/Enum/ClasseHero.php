<?php
namespace App\Enum;

enum ClasseHero: string
{
    case Guerrier = 'guerrier';
    case Mage = 'mage';
    case Voleur = 'voleur';

    public function emoji(): string
    {
        return match ($this) {
            ClasseHero::Guerrier => '🪓',
            ClasseHero::Mage     => '🔮',
            ClasseHero::Voleur   => '🗡️',
        };
    }

    public function bonusAttaque(): int
    {
        return match ($this) {
            ClasseHero::Guerrier => 4,
            ClasseHero::Mage     => 0,
            ClasseHero::Voleur   => 2,
        };
    }

    public function pvDeDepart(): int
    {
        return match ($this) {
            ClasseHero::Guerrier => 120,
            ClasseHero::Mage     => 80,
            ClasseHero::Voleur   => 95,
        };
    }
}
