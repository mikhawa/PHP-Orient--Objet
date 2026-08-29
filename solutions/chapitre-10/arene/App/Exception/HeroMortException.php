<?php
namespace App\Exception;

class HeroMortException extends \RuntimeException
{
    public function __construct(public readonly string $nomDuHero)
    {
        parent::__construct("💀 $nomDuHero est déjà tombé au combat.");
    }
}
