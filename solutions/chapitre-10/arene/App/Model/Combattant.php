<?php
namespace App\Model;

/**
 * Le CONTRAT du combat. L'Arène ne connaît que cette interface :
 * elle peut faire combattre n'importe quoi qui la respecte.
 */
interface Combattant
{
    public function attaquer(Combattant $cible): void;
    public function recevoirDegats(int $degats): void;
    public function estVivant(): bool;
    public function getName(): string;
}
