<?php
namespace App\Exception;

class ArenaVideException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("🏟️ L'arène est vide : il faut au moins deux héros !");
    }
}
