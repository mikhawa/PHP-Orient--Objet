<?php
/**
 * Corrigé 1.A — Le Tamagotchi
 * Encapsulation, promotion de propriétés, setters gardiens, __toString().
 */

declare(strict_types=1);

class Tamagotchi
{
    private int $faim = 50;
    private int $energie = 50;
    private int $humeur = 50;
    private bool $enFuite = false;

    // Promotion de propriétés (PHP 8.0) pour le nom uniquement.
    public function __construct(private string $nom) {}

    // ------------------------------------------------------------- Actions

    public function manger(): void
    {
        if ($this->estParti()) { return; }
        $this->setFaim($this->faim - 30);
        $this->setEnergie($this->energie + 5);
        echo "🍽️  {$this->nom} engloutit son repas." . PHP_EOL;
        $this->verifierSante();
    }

    public function dormir(): void
    {
        if ($this->estParti()) { return; }
        $this->setEnergie($this->energie + 40);
        $this->setFaim($this->faim + 10);
        echo "😴 {$this->nom} pique un roupillon." . PHP_EOL;
        $this->verifierSante();
    }

    public function jouer(): void
    {
        if ($this->estParti()) { return; }
        $this->setHumeur($this->humeur + 25);
        $this->setEnergie($this->energie - 20);
        $this->setFaim($this->faim + 15);
        echo "🎾 {$this->nom} s'amuse comme un petit fou." . PHP_EOL;
        $this->verifierSante();
    }

    public function parler(): void
    {
        // match(true) : le premier cas vrai gagne, l'ordre compte !
        $phrase = match (true) {
            $this->enFuite       => "… (silence, il est parti)",
            $this->faim >= 80    => "J'AI FAIM ! Nourris-moi, humain !",
            $this->energie <= 20 => "Zzz… je tiens plus debout…",
            $this->humeur <= 20  => "Je m'ennuie tellement que je compte les pixels.",
            $this->humeur >= 80  => "La vie est belle ! 🌈",
            default              => "Tout va bien, merci de demander.",
        };

        echo "💬 {$this->nom} : « $phrase »" . PHP_EOL;
    }

    // ------------------------------------------------------------- Setters
    // Les setters sont les GARDES DU CORPS : les valeurs restent dans [0, 100].

    private function setFaim(int $valeur): void
    {
        $this->faim = $this->borner($valeur);
    }

    private function setEnergie(int $valeur): void
    {
        $this->energie = $this->borner($valeur);
    }

    private function setHumeur(int $valeur): void
    {
        $this->humeur = $this->borner($valeur);
    }

    private function borner(int $valeur): int
    {
        return max(0, min(100, $valeur));
    }

    /** Une créature en fuite ne réagit plus à rien. */
    private function estParti(): bool
    {
        if ($this->enFuite) {
            echo "🕳️  {$this->nom} n'est plus là pour ça…" . PHP_EOL;
            return true;
        }
        return false;
    }

    private function verifierSante(): void
    {
        if (!$this->enFuite && ($this->faim >= 100 || $this->energie <= 0)) {
            $this->enFuite = true;
            echo "💀 {$this->nom} s'est enfui pour trouver une meilleure famille !" . PHP_EOL;
        }
    }

    // ------------------------------------------------------------ Affichage

    public function __toString(): string
    {
        if ($this->enFuite) {
            return "🕳️  {$this->nom} — porté disparu";
        }

        return sprintf(
            '🐣 %s — faim: %d/100 | énergie: %d/100 | humeur: %d/100',
            $this->nom, $this->faim, $this->energie, $this->humeur
        );
    }
}

// ---------------------------------------------------------------- Une journée

$pixel = new Tamagotchi('Pixel');

echo $pixel . PHP_EOL;
$pixel->parler();

echo PHP_EOL . "--- Le matin ---" . PHP_EOL;
$pixel->manger();
$pixel->jouer();
echo $pixel . PHP_EOL;

echo PHP_EOL . "--- L'après-midi ---" . PHP_EOL;
$pixel->dormir();
$pixel->jouer();
$pixel->parler();
echo $pixel . PHP_EOL;

echo PHP_EOL . "--- Le soir ---" . PHP_EOL;
$pixel->manger();
$pixel->dormir();
echo $pixel . PHP_EOL;
$pixel->parler();

echo PHP_EOL . "--- Le maître part en vacances sans prévenir ---" . PHP_EOL;
for ($jour = 1; $jour <= 6; $jour++) {
    $pixel->jouer();
}
echo $pixel . PHP_EOL;
$pixel->parler();
