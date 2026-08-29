<?php
/**
 * Corrigé 1.B — La fabrique de super-héros
 * Arguments nommés (PHP 8.0) et propriété readonly (PHP 8.1).
 */

declare(strict_types=1);

class SuperHeros
{
    // 3. Propriété readonly : initialisable UNE SEULE FOIS, depuis la classe.
    public readonly string $identiteSecrete;

    public function __construct(
        private string $nom,
        private string $pouvoir = 'aucun',
        private string $couleurCape = 'rouge',
        private bool $porteSlipParDessus = false,
    ) {
        // On la fabrique ici : après le constructeur, plus personne n'y touche.
        $this->identiteSecrete = strrev(strtolower(str_replace(' ', '', $nom)));
    }

    public function sePresenter(): string
    {
        $slip = $this->porteSlipParDessus
            ? 'et OUI je porte mon slip par-dessus mon costume !'
            : 'et non, je ne porte pas mon slip par-dessus, je ne suis plus un enfant.';

        return sprintf(
            'Je suis %s, mon pouvoir est %s, ma cape est %s, %s',
            $this->nom, $this->pouvoir, $this->couleurCape, $slip
        );
    }
}

// ------------------------------------------- 1. Instanciation avec arguments nommés

$heros = [
    // Tous les paramètres, dans l'ordre : la version « à l'ancienne ».
    new SuperHeros('Capitaine Gaufre', 'de lancer du sucre impalpable', 'dorée', true),

    // Arguments nommés : on ne précise QUE ce qui change…
    new SuperHeros('Madame Sieste', pouvoir: 'de dormir 19 h par jour'),

    // …et dans l'ordre que l'on veut !
    new SuperHeros(porteSlipParDessus: true, nom: 'Slipman', couleurCape: 'à pois'),

    // Le héros minimaliste : que des valeurs par défaut.
    new SuperHeros('Monsieur Rien'),
];

foreach ($heros as $h) {
    echo '🦸 ' . $h->sePresenter() . PHP_EOL;
}

// ------------------------------------------------ 3. La propriété readonly

$gaufre = $heros[0];

echo PHP_EOL . "🕵️  Identité secrète (lecture publique) : {$gaufre->identiteSecrete}" . PHP_EOL;

try {
    // Interdit : readonly = une seule initialisation, dans la classe.
    $gaufre->identiteSecrete = 'BruceWayne';
} catch (Error $e) {
    echo "⛔ Tentative de modification : " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . <<<TXT
    💡 À retenir : readonly donne le confort d'une propriété publique
       (lecture directe, pas de getter) AVEC la sécurité d'une propriété
       privée (aucune modification possible après l'initialisation).
       Un setter n'y changerait rien : même depuis l'intérieur de la classe,
       une deuxième écriture lève une Error.
    TXT . PHP_EOL;
