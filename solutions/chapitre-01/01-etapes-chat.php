<?php
/**
 * Corrigé 1.1 à 1.7 — La classe Chat, étape par étape
 *
 * Ce fichier est l'ÉTAT FINAL, après avoir fait tous les exercices dans
 * l'ordre. Chaque commentaire indique à quel exercice correspond quoi.
 */

declare(strict_types=1);

class Chat
{
    // Exercice 1.5 : la version courte du constructeur.
    // `public` et `private` devant les paramètres créent les propriétés
    // automatiquement — plus besoin de les déclarer au-dessus.
    public function __construct(
        public string $nom,
        private int $age,      // exercice 1.6 : passé en private
    ) {}

    // Exercice 1.2 : la première méthode
    public function miauler(): string
    {
        return 'Miaou !';
    }

    // Exercice 1.3 : $this = « l'objet en cours »
    public function sePresenter(): string
    {
        return 'Je suis ' . $this->nom . ' et j\'ai ' . $this->age . ' ans.';
    }

    // Exercice 1.6 : le getter, pour lire depuis l'extérieur
    public function getAge(): int
    {
        return $this->age;
    }

    // Exercice 1.7 : le setter, qui VÉRIFIE avant d'accepter
    public function setAge(int $age): void
    {
        if ($age < 0) {
            echo '⛔ Un âge négatif ? Non. On garde ' . $this->age . '.' . PHP_EOL;
            return;
        }

        $this->age = $age;
    }
}

// ─────────────────────────── Exercices 1.1 à 1.4 ───────────────────────────

$chat = new Chat('Félix', 3);

echo $chat->nom . PHP_EOL;              // 1.1
echo $chat->getAge() . PHP_EOL;         // 1.6 (avant, c'était $chat->age)
echo $chat->miauler() . PHP_EOL;        // 1.2
echo $chat->sePresenter() . PHP_EOL;    // 1.3

// 1.4 : un deuxième chat, en une seule ligne
$chat2 = new Chat('Grosminet', 5);
echo $chat2->sePresenter() . PHP_EOL;

// ─────────────────────────── Exercice 1.6 ───────────────────────────

echo PHP_EOL . '── La propriété privée ──' . PHP_EOL;

try {
    // $age est private : interdit depuis l'extérieur
    echo $chat->age;
} catch (Error $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}

echo '✅ Mais le getter fonctionne : ' . $chat->getAge() . PHP_EOL;

// ─────────────────────────── Exercice 1.7 ───────────────────────────

echo PHP_EOL . '── Le setter qui vérifie ──' . PHP_EOL;

$chat->setAge(4);
echo $chat->getAge() . PHP_EOL;     // 4

$chat->setAge(-50);                 // refusé
echo $chat->getAge() . PHP_EOL;     // toujours 4

echo PHP_EOL . <<<'TXT'
💡 CE QUE VOUS AVEZ APPRIS DANS CE CHAPITRE

   class        déclarer un modèle d'objet
   new          fabriquer un objet à partir du modèle
   ->           accéder à une propriété ou appeler une méthode
   $this        « l'objet en cours », à l'intérieur de la classe
   __construct  la méthode appelée automatiquement par new
   public       accessible de partout
   private      accessible seulement dans la classe
   getter       méthode publique pour LIRE une propriété privée
   setter       méthode publique pour ÉCRIRE, avec vérification

TXT;
