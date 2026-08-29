<?php
namespace App\Service;

// On importe la classe d'un AUTRE namespace pour pouvoir écrire `Sort`.
use App\Model\Sort;

class Incantation
{
    public function lancer(Sort $sort): string
    {
        return "🪄 Abracadabra ! {$sort->nom} lancé pour {$sort->mana} mana.";
    }
}
