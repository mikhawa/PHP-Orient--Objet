<?php
namespace App\Model;

abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        $this->hydrate($tab);
    }

    protected function hydrate(array $assoc): void
    {
        foreach ($assoc as $clef => $valeur) {
            $methode = 'set' . str_replace('_', '', ucwords((string) $clef, '_'));
            if (method_exists($this, $methode)) {
                $this->$methode($valeur);
            }
        }
    }
}
