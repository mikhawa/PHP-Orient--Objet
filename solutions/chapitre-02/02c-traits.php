<?php
/**
 * Corrigé 2.C — Les super-pouvoirs en kit
 * Les traits : la réponse de PHP à l'absence d'héritage multiple.
 */

declare(strict_types=1);

trait Vole
{
    // PHP 8.2 : un trait peut contenir des constantes.
    public const int ALTITUDE_MAX = 10000;

    public function voler(): string
    {
        return "🕊️  {$this->nom} s'envole (plafond : " . self::ALTITUDE_MAX . " m)";
    }
}

trait CracheDuFeu
{
    public function cracherDuFeu(): string
    {
        return "🔥 {$this->nom} crache une gerbe de flammes. Les pompiers arrivent.";
    }
}

trait Invisible
{
    private bool $visible = true;

    public function disparaitre(): string
    {
        $this->visible = !$this->visible;

        return $this->visible
            ? "👀 {$this->nom} réapparaît, l'air de rien."
            : "💨 {$this->nom} disparaît complètement.";
    }
}

class Dragon
{
    use Vole, CracheDuFeu; // deux traits : pas de limite !

    public function __construct(public string $nom) {}
}

class Fantome
{
    use Vole, Invisible;

    public function __construct(public string $nom) {}
}

class Politicien
{
    use Invisible; // surtout après les élections

    public function __construct(public string $nom) {}
}

// ---------------------------------------------------------------- Utilisation

$smaug = new Dragon('Smaug');
echo $smaug->voler() . PHP_EOL;
echo $smaug->cracherDuFeu() . PHP_EOL;

echo PHP_EOL;
$casper = new Fantome('Casper');
echo $casper->voler() . PHP_EOL;
echo $casper->disparaitre() . PHP_EOL;
echo $casper->disparaitre() . PHP_EOL;

echo PHP_EOL;
$elu = new Politicien('Monsieur Promesse');
echo $elu->disparaitre() . PHP_EOL;

// -------------------------------------------------- 3. Pourquoi pas l'héritage ?

echo PHP_EOL . <<<'TXT'
    ❓ POURQUOI DES TRAITS PLUTÔT QUE « Dragon extends Volant » ?

    1. PHP n'autorise QU'UN SEUL parent. Avec l'héritage, le Dragon devrait
       choisir : soit il hérite de Volant, soit de CracheurDeFeu. Impossible
       d'avoir les deux. Avec les traits, il prend les deux — et pourrait en
       prendre dix.

    2. « Dragon EST UN Volant » est un mensonge sémantique. Un dragon n'est pas
       un « volant » : il SAIT voler. L'héritage exprime « est un », le trait
       exprime « sait faire ». Fantôme et Dragon savent tous deux voler sans
       avoir le moindre ancêtre commun — et c'est très bien ainsi.

    3. Les combinaisons exploseraient : avec 3 pouvoirs, il faudrait des classes
       VolantCracheur, VolantInvisible, CracheurInvisible, VolantCracheurInvisible…
       Les traits se composent à la carte, sans hiérarchie artificielle.

    ⚠️  LIMITE À CONNAÎTRE : si deux traits fournissent une méthode du même nom,
       PHP refuse de choisir et lève une erreur fatale. Il faut arbitrer avec
       insteadof et as (voir le manuel : language.oop5.traits).
    TXT . PHP_EOL;

// Démonstration du conflit et de sa résolution
trait Nageur { public function seDeplacer(): string { return 'nage 🏊'; } }
trait Coureur { public function seDeplacer(): string { return 'court 🏃'; } }

class Triathlete
{
    use Nageur, Coureur {
        Nageur::seDeplacer insteadof Coureur;   // en cas de conflit, Nageur gagne
        Coureur::seDeplacer as courir;          // et on garde l'autre sous un alias
    }
}

echo PHP_EOL . "Résolution de conflit :" . PHP_EOL;
$t = new Triathlete();
echo '  seDeplacer() → ' . $t->seDeplacer() . PHP_EOL;
echo '  courir()     → ' . $t->courir() . PHP_EOL;
