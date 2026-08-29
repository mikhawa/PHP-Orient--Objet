<?php
namespace App\Model;

class Sort
{
    public function __construct(
        public readonly string $nom,
        public readonly int $mana,
    ) {}
}
