<?php
namespace App\Service;

use App\Exception\ArenaVideException;
use App\Manager\HeroManager;
use App\Model\Hero;

class Arene
{
    private const int TOURS_MAX = 200;

    public function __construct(private HeroManager $manager) {}

    /**
     * Fait combattre deux héros tour par tour et renvoie le vainqueur.
     * Les victoires/défaites sont persistées EN TRANSACTION.
     */
    public function combat(Hero $a, Hero $b, bool $verbeux = true): Hero
    {
        if ($a === $b) {
            throw new ArenaVideException();
        }

        $a->soigner();
        $b->soigner();

        if ($verbeux) {
            printf("⚔️  %s vs %s%s", $a, $b, PHP_EOL);
        }

        $tour = 0;

        while ($a->estVivant() && $b->estVivant() && $tour < self::TOURS_MAX) {
            $tour++;

            $this->frappe($a, $b, $tour, $verbeux);

            if ($b->estVivant()) {
                $this->frappe($b, $a, $tour, $verbeux);
            }
        }

        $vainqueur = $a->estVivant() ? $a : $b;
        $perdant = $vainqueur === $a ? $b : $a;

        if ($verbeux) {
            printf("   💀 %s tombe au combat. %s l'emporte !%s%s",
                $perdant->getName(), $vainqueur->getName(), PHP_EOL, PHP_EOL);
        }

        $this->enregistrerResultat($vainqueur, $perdant);

        return $vainqueur;
    }

    private function frappe(Hero $attaquant, Hero $cible, int $tour, bool $verbeux): void
    {
        $pvAvant = $cible->getPv();
        $attaquant->attaquer($cible);
        $degats = $pvAvant - $cible->getPv();

        if (!$verbeux) {
            $attaquant->viderMessages();
            $cible->viderMessages();

            return;
        }

        // On récupère les messages produits par les deux camps (attaque + esquive).
        $messages = array_merge($attaquant->viderMessages(), $cible->viderMessages());

        printf(
            "   Tour %-2d : %s%s — %s : %s%s",
            $tour,
            implode(' / ', $messages),
            $degats > 0 ? " ({$degats} dégâts)" : '',
            $cible->getName(),
            $cible->barreDeVie(),
            PHP_EOL
        );
    }

    private function enregistrerResultat(Hero $vainqueur, Hero $perdant): void
    {
        $vainqueur->ajouterVictoire();
        $perdant->ajouterDefaite();

        // L'Arène ne sait pas ce qu'est une transaction ni un PDO :
        // elle demande au manager d'enregistrer, point. Séparation des
        // responsabilités : le combat ici, la persistance là-bas.
        $this->manager->enregistrerResultat($vainqueur, $perdant);
    }
}
