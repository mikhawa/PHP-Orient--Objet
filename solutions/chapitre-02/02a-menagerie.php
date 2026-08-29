<?php
/**
 * Corrigé 2.A — La ménagerie qui parle
 * Classe abstraite, méthode abstraite, #[\Override], polymorphisme.
 */

declare(strict_types=1);

abstract class Animal
{
    public function __construct(protected string $nom) {}

    // Méthode ABSTRAITE : pas de corps. Chaque enfant DOIT la définir.
    abstract public function crier(): string;

    // Méthode CONCRÈTE : écrite une seule fois, héritée par tous.
    // Elle appelle crier() sans savoir laquelle sera exécutée : c'est
    // au moment de l'exécution que PHP choisit la version de l'enfant.
    public function sePresenter(): string
    {
        return "Je suis {$this->nom} et je fais : " . $this->crier();
    }
}

class Chien extends Animal
{
    #[\Override]
    public function crier(): string
    {
        return 'Wouf !';
    }
}

class Chat extends Animal
{
    #[\Override]
    public function crier(): string
    {
        return 'Miaou !';
    }
}

class Perroquet extends Animal
{
    // Le perroquet a besoin d'un paramètre supplémentaire :
    // on redéfinit le constructeur et on appelle celui du parent.
    public function __construct(string $nom, private string $phrase)
    {
        parent::__construct($nom);
    }

    #[\Override]
    public function crier(): string
    {
        return "{$this->phrase} Croâ ! {$this->phrase}";
    }
}

// ---------------------------------------------------- Le polymorphisme en action

$menagerie = [
    new Chien('Rex'),
    new Chat('Félix'),
    new Perroquet('Coco', "À l'abordage !"),
];

// UNE SEULE boucle, trois comportements différents : c'est ça, le polymorphisme.
foreach ($menagerie as $animal) {
    echo $animal->sePresenter() . PHP_EOL;
}

// ----------------------------------------------- Ce qui est INTERDIT (et pourquoi)

echo PHP_EOL . "--- Tentatives interdites ---" . PHP_EOL;

try {
    // Une classe abstraite est un modèle incomplet : on ne peut pas l'instancier.
    $mystere = new Animal('Truc');
} catch (Error $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . <<<'TXT'
    💡 À retenir :
       - abstract sur la CLASSE  → interdit de l'instancier ;
       - abstract sur la MÉTHODE → oblige chaque enfant à la définir ;
       - #[\Override] (PHP 8.3)  → PHP vérifie que la méthode redéfinit bien
         quelque chose. Une faute de frappe devient une erreur fatale au
         chargement au lieu d'une méthode fantôme jamais appelée.
    TXT . PHP_EOL;
