<?php
/**
 * Corrigé 2.B — Le tournoi
 * Interface, polymorphisme, duel générique.
 */

declare(strict_types=1);

interface Combattant
{
    public function attaquer(Combattant $cible): void;
    public function recevoirDegats(int $degats): void;
    public function estVivant(): bool;
    public function getNom(): string;
}

class Chevalier implements Combattant
{
    public function __construct(
        private string $nom,
        private int $pv = 100,
    ) {}

    public function attaquer(Combattant $cible): void
    {
        $degats = random_int(8, 15);
        echo "   ⚔️  {$this->nom} frappe ({$degats} dégâts)" . PHP_EOL;
        $cible->recevoirDegats($degats);
    }

    public function recevoirDegats(int $degats): void
    {
        $this->pv = max(0, $this->pv - $degats);
    }

    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
}

class Magicien implements Combattant
{
    public function __construct(
        private string $nom,
        private int $pv = 80,
    ) {}

    public function attaquer(Combattant $cible): void
    {
        // Puissant… quand ça marche.
        if (random_int(0, 1) === 0) {
            echo "   💨 {$this->nom} rate son sort (fizzle…)" . PHP_EOL;
            return;
        }

        $degats = random_int(25, 40);
        echo "   🔥 {$this->nom} lance une BOULE DE FEU ({$degats} dégâts)" . PHP_EOL;
        $cible->recevoirDegats($degats);
    }

    public function recevoirDegats(int $degats): void
    {
        $this->pv = max(0, $this->pv - $degats);
    }

    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
}

class Poule implements Combattant
{
    // 1 dégât par coup, mais 500 PV : la stratégie de l'usure. 🐔
    public function __construct(
        private string $nom,
        private int $pv = 500,
    ) {}

    public function attaquer(Combattant $cible): void
    {
        echo "   🐔 {$this->nom} donne un coup de bec (1 dégât)" . PHP_EOL;
        $cible->recevoirDegats(1);
    }

    public function recevoirDegats(int $degats): void
    {
        $this->pv = max(0, $this->pv - $degats);
    }

    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
}

/**
 * Cette fonction ne connaît AUCUNE classe concrète : elle ne connaît que
 * le contrat Combattant. On peut lui donner n'importe quel combattant,
 * présent ou futur, sans jamais la modifier. C'est tout l'intérêt d'une interface.
 */
function duel(Combattant $a, Combattant $b, bool $verbeux = true): Combattant
{
    if ($verbeux) {
        echo "⚔️  {$a->getNom()} vs {$b->getNom()}" . PHP_EOL;
    }

    $tour = 0;
    while ($a->estVivant() && $b->estVivant() && $tour < 2000) {
        $tour++;
        $a->attaquer($b);
        if ($b->estVivant()) {
            $b->attaquer($a);
        }
    }

    $vainqueur = $a->estVivant() ? $a : $b;

    if ($verbeux) {
        echo "   🏆 {$vainqueur->getNom()} l'emporte en {$tour} tours !" . PHP_EOL . PHP_EOL;
    }

    return $vainqueur;
}

// ------------------------------------------------------------- Le mini-tournoi

echo "🏟️  TOURNOI À QUATRE" . PHP_EOL . PHP_EOL;

echo "--- Demi-finale 1 ---" . PHP_EOL;
$finaliste1 = duel(new Chevalier('Ragnar'), new Magicien('Merlin'));

echo "--- Demi-finale 2 ---" . PHP_EOL;
$finaliste2 = duel(new Chevalier('Lancelot'), new Poule('Gertrude'));

echo "--- FINALE ---" . PHP_EOL;
$champion = duel($finaliste1, $finaliste2);

echo "👑 CHAMPION : {$champion->getNom()}" . PHP_EOL;

// ---------------------------------------- Qui gagne le plus souvent ? (1000 duels)

echo PHP_EOL . "📊 STATISTIQUES SUR 1000 DUELS (combattants neufs à chaque fois)" . PHP_EOL;

$affiches = [
    'Chevalier vs Magicien' => fn() => [new Chevalier('Chevalier'), new Magicien('Magicien')],
    'Chevalier vs Poule'    => fn() => [new Chevalier('Chevalier'), new Poule('Poule')],
    'Magicien vs Poule'     => fn() => [new Magicien('Magicien'), new Poule('Poule')],
];

foreach ($affiches as $libelle => $fabrique) {
    $victoires = [];

    // ob_start() capture tout ce que les combattants affichent : on veut
    // seulement les statistiques, pas 1000 combats détaillés à l'écran.
    ob_start();
    for ($i = 0; $i < 1000; $i++) {
        [$x, $y] = $fabrique();
        $nom = duel($x, $y, verbeux: false)->getNom();
        $victoires[$nom] = ($victoires[$nom] ?? 0) + 1;
    }
    ob_end_clean();
    arsort($victoires);
    $resume = [];
    foreach ($victoires as $nom => $nb) {
        $resume[] = "$nom : " . ($nb / 10) . ' %';
    }
    printf("   %-24s %s%s", $libelle, implode(' | ', $resume), PHP_EOL);
}

// ------------------------------------------------- L'analyse (spoiler : la poule perd)

echo PHP_EOL . <<<'TXT'
    🐔 VERDICT : contrairement à l'intuition, la poule perd TOUJOURS.

    Faisons le calcul, c'est plus intéressant que le combat lui-même :
      - le Chevalier inflige ~11,5 dégâts/tour → il vide les 500 PV de la
        poule en ~44 tours ;
      - la poule inflige 1 dégât/tour → il lui faut 100 tours pour venir à
        bout des 100 PV du chevalier.
      44 < 100 : le chevalier gagne avant. Toujours.

    ⚖️ À VOUS DE JOUER : rééquilibrez la poule pour qu'elle gagne ~50 % des
       duels contre le Chevalier. Deux pistes (une seule suffit) :
         a) augmenter ses PV : il en faut ~1150 pour tenir 100 tours ;
         b) augmenter ses dégâts : à 5 dégâts/tour, il lui faut 20 tours
            pour tuer le chevalier, qui en met 44 à la tuer → elle gagne.
       Modifiez les valeurs ci-dessus, relancez, et regardez les statistiques
       bouger. C'est exactement le travail d'un game designer. 🎮
    TXT . PHP_EOL;
