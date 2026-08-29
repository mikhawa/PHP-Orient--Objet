<?php
namespace App\Model;

use App\Enum\ClasseHero;

class Guerrier extends Hero
{
    public function __construct(array $tab = [])
    {
        $this->pv = $this->pvMax = ClasseHero::Guerrier->pvDeDepart();
        parent::__construct($tab);
    }

    /** Pouvoir : 10 % de chances de coup critique (dégâts doublés). */
    #[\Override]
    protected function calculerDegats(): int
    {
        $base = random_int(1, $this->forceAttaque) + ClasseHero::Guerrier->bonusAttaque();

        if (random_int(1, 100) <= 10) {
            $this->noter("💥 COUP CRITIQUE de {$this->name} !");
            return $base * 2;
        }

        $this->noter("🪓 {$this->name} frappe");

        return $base;
    }
}
