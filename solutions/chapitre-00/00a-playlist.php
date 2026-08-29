<?php
/**
 * Corrigé 0.A — La playlist du stage
 * Conversion d'un code procédural en orienté objet.
 */

declare(strict_types=1);

class Morceau
{
    // Promotion de propriétés (PHP 8.0) : déclaration + affectation en une fois.
    public function __construct(
        public readonly string $titre,
        public readonly string $artiste,
        public readonly int $duree, // en secondes
    ) {}

    /** Formate une durée en mm:ss */
    public function dureeFormatee(): string
    {
        return sprintf('%d:%02d', intdiv($this->duree, 60), $this->duree % 60);
    }
}

class Playlist
{
    /** @var Morceau[] */
    private array $morceaux = [];

    public function __construct(private string $nom) {}

    public function ajouter(Morceau $morceau): void
    {
        $this->morceaux[] = $morceau;
    }

    public function dureeTotale(): int
    {
        $total = 0;
        foreach ($this->morceaux as $morceau) {
            $total += $morceau->duree;
        }
        return $total;
        // Variante plus courte :
        // return array_sum(array_map(fn(Morceau $m) => $m->duree, $this->morceaux));
    }

    public function afficher(): void
    {
        $total = $this->dureeTotale();
        printf(
            "🎵 Playlist « %s » — %d morceaux, durée totale %d:%02d%s",
            $this->nom,
            count($this->morceaux),
            intdiv($total, 60),
            $total % 60,
            PHP_EOL
        );

        foreach ($this->morceaux as $i => $morceau) {
            printf(
                "%d. %s — %s (%s)%s",
                $i + 1,
                $morceau->titre,
                $morceau->artiste,
                $morceau->dureeFormatee(),
                PHP_EOL
            );
        }
    }

    // --- Bonus ---

    public function morceauAleatoire(): Morceau
    {
        return $this->morceaux[array_rand($this->morceaux)];
    }

    public function plusLong(): Morceau
    {
        $plusLong = $this->morceaux[0];
        foreach ($this->morceaux as $morceau) {
            if ($morceau->duree > $plusLong->duree) {
                $plusLong = $morceau;
            }
        }
        return $plusLong;
    }
}

// ---------------------------------------------------------------- Utilisation

$playlist = new Playlist('Pause café');
$playlist->ajouter(new Morceau('PHP Anthem', 'The Coders', 210));
$playlist->ajouter(new Morceau('Objets trouvés', 'DJ Class', 185));
$playlist->ajouter(new Morceau('Boucle infinie', 'While Trio', 240));

$playlist->afficher();

echo PHP_EOL . '🔀 Au hasard : ' . $playlist->morceauAleatoire()->titre . PHP_EOL;
echo '⏱️  Le plus long : ' . $playlist->plusLong()->titre . PHP_EOL;
