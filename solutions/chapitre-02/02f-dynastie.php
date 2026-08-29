<?php
/**
 * Corrigé 2.F — La dynastie royale
 * parent::, chaîne de redéfinitions, final, et le piège de la récursion.
 */

declare(strict_types=1);

class Monarque
{
    public function __construct(protected string $nom) {}

    public function titreComplet(): string
    {
        return 'Sa Majesté';
    }
}

class Roi extends Monarque
{
    #[\Override]
    public function titreComplet(): string
    {
        // parent:: remonte d'un cran dans la hiérarchie et exécute
        // la version du parent, PUIS on enrichit le résultat.
        return parent::titreComplet() . " le Roi {$this->nom}";
    }
}

class RoiSoleil extends Roi
{
    #[\Override]
    public function titreComplet(): string
    {
        // Ici parent:: désigne Roi, qui lui-même appelle Monarque.
        // La chaîne se déroule de bas en haut, puis se reconstruit de haut en bas.
        return parent::titreComplet() . ', lumière de la POO';
    }
}

// ---------------------------------------------------------------- Utilisation

$louis = new RoiSoleil('Louis');
echo $louis->titreComplet() . PHP_EOL;

echo PHP_EOL . "🔍 La chaîne d'appels, pas à pas :" . PHP_EOL;
echo <<<'TXT'
   1. RoiSoleil::titreComplet()  appelle  parent::titreComplet()
   2.       Roi::titreComplet()  appelle  parent::titreComplet()
   3.  Monarque::titreComplet()  renvoie  "Sa Majesté"
   4.       Roi  y ajoute        " le Roi Louis"
   5. RoiSoleil y ajoute         ", lumière de la POO"
TXT . PHP_EOL;

// -------------------------------------------------------------- 5. final

echo PHP_EOL . <<<'TXT'
    🔒 SI Roi::titreComplet() ÉTAIT DÉCLARÉE final :

         final public function titreComplet(): string { ... }

       → RoiSoleil ne compile plus :
         Fatal error: Cannot override final method Roi::titreComplet()

       POURQUOI UN DÉVELOPPEUR FERAIT-IL ÇA ? Pour garantir un comportement
       que personne ne doit pouvoir contourner : un calcul de prix, une
       vérification de sécurité, une signature cryptographique. `final` dit
       « ce comportement fait partie du contrat, on ne le négocie pas ».
       À utiliser avec parcimonie : une classe finale est aussi une classe
       qu'on ne peut plus étendre ni doubler dans un test.
    TXT . PHP_EOL;

// ------------------------------------------------ La question de réflexion

echo PHP_EOL . <<<'TXT'
    ☠️  ET SI ON ÉCRIVAIT $this->titreComplet() AU LIEU DE parent:: ?

       $this->titreComplet() part TOUJOURS de la classe réelle de l'objet
       (ici RoiSoleil) et redescend la chaîne de redéfinitions. Donc
       RoiSoleil::titreComplet() s'appellerait elle-même, indéfiniment :

         RoiSoleil::titreComplet()
           → $this->titreComplet()  = RoiSoleil::titreComplet()
             → $this->titreComplet() = RoiSoleil::titreComplet()
               → … jusqu'au crash

       Résultat : « Fatal error: Maximum function nesting level reached »
       ou un segfault (PHP n'a pas de limite de récursion par défaut sans
       Xdebug — le processus consomme toute la mémoire de la pile).

       RÈGLE : parent:: = « la version au-dessus de MOI dans le code ».
               $this->  = « la version la plus spécialisée de l'objet réel ».
    TXT . PHP_EOL;

// Démonstration contrôlée de la récursion (avec un garde-fou)
class RoiEtourdi extends Roi
{
    private int $profondeur = 0;

    #[\Override]
    public function titreComplet(): string
    {
        if (++$this->profondeur > 5) {
            return '💥 STOP — récursion infinie détectée à la profondeur 5';
        }

        // ⚠️ LE BUG : $this au lieu de parent
        return $this->titreComplet();
    }
}

echo PHP_EOL . "Démonstration (avec garde-fou pour ne pas faire planter la machine) :" . PHP_EOL;
echo '  ' . (new RoiEtourdi('Étourdi'))->titreComplet() . PHP_EOL;
