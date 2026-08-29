<?php
namespace Guildes\Alchimistes;

class Potion
{
    public function __construct(
        public readonly string $nom,
        public readonly int $degatsExplosion,
    ) {}

    public function boire(): string
    {
        return "💥 BOUM ({$this->degatsExplosion} dégâts). "
            . 'Les alchimistes déclinent toute responsabilité.';
    }
}

const DEVISE = "Si ça n'explose pas, c'est que ce n'est pas fini.";
