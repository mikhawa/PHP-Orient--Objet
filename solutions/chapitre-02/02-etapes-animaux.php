<?php
/**
 * Corrigé 2.1 à 2.8 — La ménagerie, étape par étape
 * État final du fichier après tous les exercices.
 */

declare(strict_types=1);

// ─────────── 2.7 : la classe est ABSTRAITE (on ne peut pas l'instancier)
abstract class Animal
{
    public function __construct(public string $nom) {}

    public function manger(): string
    {
        return $this->nom . ' mange.';
    }

    // 2.7 : méthode abstraite = pas de corps, chaque enfant DOIT l'écrire
    abstract public function crier(): string;
}

class Chien extends Animal
{
    #[\Override]
    public function crier(): string
    {
        return $this->nom . ' fait : Wouf !';
    }
}

class Chat extends Animal
{
    #[\Override]
    public function crier(): string
    {
        return $this->nom . ' fait : Miaou !';
    }

    // 2.6 : on COMPLÈTE le parent au lieu de le remplacer
    #[\Override]
    public function manger(): string
    {
        return parent::manger() . ' Puis il fait sa toilette.';
    }
}

// 2.7 (test) : un poisson, pour vérifier que crier() est bien obligatoire
class Poisson extends Animal
{
    #[\Override]
    public function crier(): string
    {
        return $this->nom . ' fait : Blub.';
    }
}

// 2.8 : un constructeur à DEUX paramètres
class Perroquet extends Animal
{
    public function __construct(string $nom, private string $phrase)
    {
        // On laisse le parent s'occuper du nom : pas de code en double.
        parent::__construct($nom);
    }

    #[\Override]
    public function crier(): string
    {
        return $this->nom . ' fait : ' . $this->phrase
            . ' Croâ ! ' . $this->phrase;
    }
}

// ─────────────────────────── 2.2 et 2.3 ───────────────────────────

$rex = new Chien('Rex');
$felix = new Chat('Félix');

echo $rex->manger() . PHP_EOL;      // méthode héritée
echo $rex->crier() . PHP_EOL;       // méthode propre
echo $felix->manger() . PHP_EOL;    // 2.6 : version redéfinie + parent::
echo $felix->crier() . PHP_EOL;

// ─────────────────────────── 2.4 et 2.8 ───────────────────────────

echo PHP_EOL . '── La boucle magique (polymorphisme) ──' . PHP_EOL;

$animaux = [
    new Chien('Rex'),
    new Chat('Félix'),
    new Chien('Médor'),
    new Poisson('Nemo'),
    new Perroquet('Coco', "À l'abordage !"),
];

// UNE boucle, cinq comportements différents, ZÉRO if.
foreach ($animaux as $animal) {
    echo $animal->crier() . PHP_EOL;
}

// ─────────────────────────── 2.7 : les garde-fous ───────────────────────────

echo PHP_EOL . '── Ce que PHP refuse ──' . PHP_EOL;

try {
    $mystere = new Animal('Truc');
} catch (Error $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . <<<'TXT'
💡 CE QUE VOUS AVEZ APPRIS

   extends            hériter : récupérer les méthodes du parent
   redéfinition       réécrire une méthode du parent dans l'enfant
   parent::methode()  appeler la version du parent depuis l'enfant
   abstract class     classe qu'on ne peut PAS instancier
   abstract function  méthode sans corps, obligatoire chez les enfants
   #[\Override]       sécurité : PHP vérifie que la méthode existe chez le parent
   polymorphisme      une boucle, des comportements différents, aucun if

   👉 RÉPONSE À LA QUESTION 2.3 : la méthode manger() n'est écrite
      QU'UNE SEULE FOIS, dans Animal. Sans héritage, il aurait fallu
      la recopier dans chacune des 5 classes — et la corriger 5 fois
      au moindre changement.

TXT;
