<?php
namespace App\Model;

use App\Enum\ClasseHero;

class Voleur extends Hero
{
    public function __construct(array $tab = [])
    {
        $this->pv = $this->pvMax = ClasseHero::Voleur->pvDeDepart();
        parent::__construct($tab);
    }

    #[\Override]
    protected function calculerDegats(): int
    {
        $this->noter("🗡️  {$this->name} porte un coup vif");

        return random_int(1, $this->forceAttaque) + ClasseHero::Voleur->bonusAttaque();
    }

    /** Pouvoir : 25 % de chances d'esquiver complètement une attaque. */
    #[\Override]
    public function recevoirDegats(int $degats): void
    {
        if (random_int(1, 100) <= 25) {
            $this->noter("💨 {$this->name} ESQUIVE !");

            return;
        }

        parent::recevoirDegats($degats);
    }
}
