<?php
namespace App;

class Incantation
{
    public function lancer(Sort $sort): string
    {
        return '🪄 ' . $sort->nom . ' lancé pour ' . $sort->mana . ' mana.';
    }
}
