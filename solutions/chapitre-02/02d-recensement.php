<?php
/**
 * Corrigé 2.D — Le compteur de population
 * Propriété statique, méthode statique, constante de classe typée et finale.
 */

declare(strict_types=1);

class Tamagotchi
{
    // Propriété STATIQUE : elle appartient à la CLASSE, pas aux instances.
    // Une seule case mémoire, partagée par toutes les créatures du monde.
    private static int $population = 0;

    // PHP 8.3 : constante typée. PHP 8.1 : final (aucun enfant ne la redéfinit).
    final public const int SEUIL_SURPOPULATION = 10;

    private int $faim = 50;
    private int $energie = 50;

    public function __construct(private string $nom)
    {
        // self:: pour accéder aux membres statiques depuis l'intérieur.
        self::$population++;
    }

    // Bonus : quand une créature s'enfuit, la population diminue.
    public function fuguer(): void
    {
        self::$population--;
        echo "💨 {$this->nom} a fugué." . PHP_EOL;
    }

    /**
     * Méthode STATIQUE : appelable sans instance, via Tamagotchi::recensement().
     * ⚠️ Aucun $this ici : il n'y a pas d'objet courant !
     */
    public static function recensement(): string
    {
        $rapport = "🌍 Population mondiale de Tamagotchis : " . self::$population;

        if (self::$population > self::SEUIL_SURPOPULATION) {
            $rapport .= PHP_EOL . "🚨 ALERTE SURPOPULATION ! Le seuil de "
                . self::SEUIL_SURPOPULATION . " est dépassé."
                . PHP_EOL . "   Les ressources en piles AAA s'épuisent."
                . PHP_EOL . "   L'ONU des créatures virtuelles est convoquée en urgence.";
        }

        return $rapport;
    }

    public static function getPopulation(): int
    {
        return self::$population;
    }
}

// ---------------------------------------------------------------- Utilisation

// La méthode statique s'appelle SANS avoir créé le moindre objet :
echo Tamagotchi::recensement() . PHP_EOL;

echo PHP_EOL . "--- Naissance de 8 créatures ---" . PHP_EOL;
$creatures = [];
foreach (['Pixel', 'Bit', 'Octet', 'Byte', 'Nibble', 'Flop', 'Cache', 'Buffer'] as $nom) {
    $creatures[] = new Tamagotchi($nom);
}
echo Tamagotchi::recensement() . PHP_EOL;

echo PHP_EOL . "--- La mode explose : 5 naissances de plus ---" . PHP_EOL;
foreach (['Kilo', 'Mega', 'Giga', 'Tera', 'Peta'] as $nom) {
    $creatures[] = new Tamagotchi($nom);
}
echo Tamagotchi::recensement() . PHP_EOL;

echo PHP_EOL . "--- Trois fugues ---" . PHP_EOL;
$creatures[0]->fuguer();
$creatures[1]->fuguer();
$creatures[2]->fuguer();
echo Tamagotchi::recensement() . PHP_EOL;

// ------------------------------------------------------- Ce qui est interdit

echo PHP_EOL . "--- Tentatives interdites ---" . PHP_EOL;

// La constante est finale : une classe enfant ne peut PAS la redéfinir.
// Décommentez pour voir l'erreur fatale :
//   class TamagotchiPro extends Tamagotchi {
//       public const int SEUIL_SURPOPULATION = 999;
//   }
// → Fatal error: TamagotchiPro::SEUIL_SURPOPULATION cannot override
//   final constant Tamagotchi::SEUIL_SURPOPULATION

echo "  (voir les commentaires du fichier : redéfinir une constante final"
    . " est une erreur fatale)" . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    💡 À retenir :
       - une propriété statique est PARTAGÉE : un seul compteur pour toute
         la classe, quel que soit le nombre d'objets créés ;
       - une méthode statique s'appelle sans instance (Classe::methode()),
         et n'a donc pas accès à $this ;
       - self:: à l'intérieur, NomDeClasse:: à l'extérieur ;
       - usage typique : compteurs, fabriques, utilitaires sans état.
         ⚠️ Ne mettez PAS tout en statique : un objet sans état n'est plus
         un objet, et le code statique est difficile à tester.
    TXT . PHP_EOL;
