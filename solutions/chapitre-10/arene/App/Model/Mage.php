<?php
namespace App\Model;

use App\Enum\ClasseHero;

class Mage extends Hero
{
    /** Le mage doit recharger un tour sur deux. */
    private bool $enRecharge = false;

    public function __construct(array $tab = [])
    {
        $this->pv = $this->pvMax = ClasseHero::Mage->pvDeDepart();
        parent::__construct($tab);
    }

    /** Pouvoir : boule de feu dévastatrice, mais un tour sur deux à recharger. */
    #[\Override]
    protected function calculerDegats(): int
    {
        if ($this->enRecharge) {
            $this->enRecharge = false;
            $this->noter("🌀 {$this->name} recharge son mana…");

            return 0;
        }

        $this->enRecharge = true;
        $degats = random_int($this->forceAttaque, $this->forceAttaque * 3);
        $this->noter("🔥 {$this->name} lance une BOULE DE FEU");

        return $degats;
    }
}
