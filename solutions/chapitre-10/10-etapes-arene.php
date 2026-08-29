<?php
/**
 * Corrigé du projet final — L'Arène des Héros, version SIMPLE
 * (celle des 6 étapes de exercices/chapitre-10.md)
 *
 * La version avancée, avec namespaces et autoload, est dans arene/.
 */

declare(strict_types=1);

// ═══════════════════════ Étape 4 : l'enum ═══════════════════════

enum ClasseHero: string
{
    case Guerrier = 'guerrier';
    case Mage = 'mage';
    case Voleur = 'voleur';

    public function emoji(): string
    {
        return match ($this) {
            ClasseHero::Guerrier => '🪓',
            ClasseHero::Mage     => '🔮',
            ClasseHero::Voleur   => '🗡️',
        };
    }
}

// ═══════════════════════ Étapes 1 et 3 : les héros ═══════════════════════

abstract class Hero
{
    public function __construct(
        protected string $nom,      // protected : les enfants en ont besoin
        protected int $pv = 100,
        protected int $force = 10,
        private int $id = 0,
    ) {}

    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPv(): int { return $this->pv; }
    public function setId(int $id): void { $this->id = $id; }

    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    public function recevoirDegats(int $degats): void
    {
        $this->pv = max(0, $this->pv - $degats);
    }

    public function attaquer(Hero $cible): void
    {
        $degats = $this->calculerDegats();

        // Si l'attaque a raté, le pouvoir a déjà affiché son message :
        // inutile d'annoncer « 0 dégât » par-dessus.
        if ($degats > 0) {
            echo '   ' . $this->nom . ' attaque (' . $degats . ' dégâts)' . PHP_EOL;
            $cible->recevoirDegats($degats);
        }
    }

    // Le point de variation : chaque classe a SON pouvoir.
    abstract protected function calculerDegats(): int;

    abstract public function getClasse(): ClasseHero;
}

class Guerrier extends Hero
{
    #[\Override]
    protected function calculerDegats(): int
    {
        $degats = random_int(1, $this->force);

        // 1 fois sur 10 : coup critique
        if (random_int(1, 10) === 1) {
            echo '   💥 COUP CRITIQUE !' . PHP_EOL;
            return $degats * 2;
        }

        return $degats;
    }

    #[\Override]
    public function getClasse(): ClasseHero
    {
        return ClasseHero::Guerrier;
    }
}

class Mage extends Hero
{
    #[\Override]
    protected function calculerDegats(): int
    {
        // 1 fois sur 2 : le sort rate
        if (random_int(1, 2) === 1) {
            echo '   💨 Le sort échoue…' . PHP_EOL;
            return 0;
        }

        return random_int(20, 35);
    }

    #[\Override]
    public function getClasse(): ClasseHero
    {
        return ClasseHero::Mage;
    }
}

class Voleur extends Hero
{
    #[\Override]
    protected function calculerDegats(): int
    {
        return random_int(1, $this->force);
    }

    // On redéfinit la RÉCEPTION des dégâts, pas l'attaque
    #[\Override]
    public function recevoirDegats(int $degats): void
    {
        // 1 fois sur 4 : esquive
        if (random_int(1, 4) === 1) {
            echo '   💨 ' . $this->nom . ' esquive !' . PHP_EOL;
            return;
        }

        parent::recevoirDegats($degats);
    }

    #[\Override]
    public function getClasse(): ClasseHero
    {
        return ClasseHero::Voleur;
    }
}

// ═══════════════════════ Étape 5 : le manager ═══════════════════════

class HeroManager
{
    public function __construct(private PDO $pdo) {}

    public function add(Hero $hero): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO hero (nom, classe) VALUES (:nom, :classe)'
        );

        $stmt->execute([
            'nom'    => $hero->getNom(),
            'classe' => $hero->getClasse()->value,   // l'enum → texte
        ]);

        $hero->setId((int) $this->pdo->lastInsertId());
    }

    public function enregistrerVictoire(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE hero SET victoires = victoires + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function enregistrerDefaite(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE hero SET defaites = defaites + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function getClassement(): array
    {
        return $this->pdo
            ->query('SELECT * FROM hero ORDER BY victoires DESC, defaites ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ═══════════════════════ Étape 2 : le combat ═══════════════════════

function combat(Hero $a, Hero $b, HeroManager $manager): Hero
{
    echo '⚔️  ' . $a->getNom() . ' contre ' . $b->getNom() . PHP_EOL;

    while ($a->estVivant() && $b->estVivant()) {
        $a->attaquer($b);

        if ($b->estVivant()) {
            $b->attaquer($a);
        }
    }

    $vainqueur = $a->estVivant() ? $a : $b;
    $perdant = $vainqueur === $a ? $b : $a;

    echo '   🏆 ' . $vainqueur->getNom() . ' gagne !' . PHP_EOL . PHP_EOL;

    $manager->enregistrerVictoire($vainqueur->getId());
    $manager->enregistrerDefaite($perdant->getId());

    return $vainqueur;
}

// ═══════════════════════ Étape 6 : le tournoi ═══════════════════════

$pdo = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$pdo->exec('
    CREATE TABLE hero (
        id        INTEGER PRIMARY KEY AUTOINCREMENT,
        nom       VARCHAR(100) NOT NULL,
        classe    VARCHAR(20) NOT NULL,
        victoires INT NOT NULL DEFAULT 0,
        defaites  INT NOT NULL DEFAULT 0
    )
');

$manager = new HeroManager($pdo);

echo '🏟️  L\'ARÈNE DES HÉROS' . PHP_EOL . PHP_EOL;

$ragnar = new Guerrier('Ragnar', 120, 14);
$merlin = new Mage('Merlin', 80);
$shadow = new Voleur('Shadow', 95, 13);
$brunhild = new Guerrier('Brunhild', 120, 15);

foreach ([$ragnar, $merlin, $shadow, $brunhild] as $hero) {
    $manager->add($hero);
}

echo '── Demi-finale 1 ──' . PHP_EOL;
$finaliste1 = combat($ragnar, $merlin, $manager);

echo '── Demi-finale 2 ──' . PHP_EOL;
$finaliste2 = combat($shadow, $brunhild, $manager);

echo '── FINALE ──' . PHP_EOL;
$champion = combat($finaliste1, $finaliste2, $manager);

// ── Le classement, lu depuis la base
echo '🏆 CLASSEMENT' . PHP_EOL;

foreach ($manager->getClassement() as $rang => $ligne) {
    $classe = ClasseHero::from($ligne['classe']);

    printf(
        "%d. %s %s — %d victoire%s, %d défaite%s%s",
        $rang + 1,
        $classe->emoji(),
        $ligne['nom'],
        $ligne['victoires'], $ligne['victoires'] > 1 ? 's' : '',
        $ligne['defaites'],  $ligne['defaites'] > 1 ? 's' : '',
        PHP_EOL
    );
}

echo PHP_EOL . '👑 Champion : ' . $champion->getNom() . PHP_EOL;
