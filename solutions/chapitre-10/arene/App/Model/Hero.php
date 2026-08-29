<?php
namespace App\Model;

use App\Enum\ClasseHero;
use App\Exception\HeroMortException;

/**
 * La classe abstraite qui porte tout le commun : données, hydratation,
 * encaissement des dégâts. Chaque classe enfant n'écrit que SON pouvoir.
 */
abstract class Hero extends AbstractModel implements Combattant
{
    protected ?int $id = null;
    protected string $name = '';
    protected int $pv = 100;
    protected int $pvMax = 100;
    protected int $forceAttaque = 10;
    protected int $victoires = 0;
    protected int $defaites = 0;

    /** Visibilité asymétrique (PHP 8.4) : lisible partout, modifiable ici seulement. */
    public private(set) int $degatsInfligesTotal = 0;

    /** Journal du dernier combat, rempli par l'Arène. */
    protected array $messages = [];

    // ─────────────────────────── Le contrat Combattant ───────────────────────────

    #[\Override]
    public function attaquer(Combattant $cible): void
    {
        if (!$this->estVivant()) {
            throw new HeroMortException($this->name);
        }

        $degats = $this->calculerDegats();

        if ($degats > 0) {
            $cible->recevoirDegats($degats);
            $this->degatsInfligesTotal += $degats;
        }
    }

    /** LE point de variation : chaque classe a son pouvoir spécial. */
    abstract protected function calculerDegats(): int;

    #[\Override]
    public function recevoirDegats(int $degats): void
    {
        $this->pv = max(0, $this->pv - $degats);
    }

    #[\Override]
    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    // ─────────────────────────────── Journal ───────────────────────────────

    protected function noter(string $message): void
    {
        $this->messages[] = $message;
    }

    public function viderMessages(): array
    {
        $messages = $this->messages;
        $this->messages = [];

        return $messages;
    }

    // ─────────────────────────────── Getters ───────────────────────────────

    public function getId(): ?int { return $this->id; }
    #[\Override]
    public function getName(): string { return $this->name; }
    public function getPv(): int { return $this->pv; }
    public function getPvMax(): int { return $this->pvMax; }
    public function getForceAttaque(): int { return $this->forceAttaque; }
    public function getVictoires(): int { return $this->victoires; }
    public function getDefaites(): int { return $this->defaites; }

    /** La classe est déduite de la classe PHP concrète : une seule source de vérité. */
    public function getClasse(): ClasseHero
    {
        return match (static::class) {
            Guerrier::class => ClasseHero::Guerrier,
            Mage::class     => ClasseHero::Mage,
            Voleur::class   => ClasseHero::Voleur,
        };
    }

    // ─────────────────────────────── Setters ───────────────────────────────

    public function setId(int|string|null $v): void
    {
        $this->id = $v === null ? null : (int) $v;
    }

    public function setName(string $name): void
    {
        $propre = trim(strip_tags($name));

        if ($propre === '') {
            throw new \InvalidArgumentException('Un héros doit avoir un nom.');
        }

        $this->name = $propre;
    }

    public function setPv(int|string $pv): void
    {
        $this->pv = max(0, (int) $pv);
        $this->pvMax = max($this->pvMax, $this->pv);
    }

    public function setForceAttaque(int|string $f): void
    {
        $this->forceAttaque = max(1, (int) $f);
    }

    public function setVictoires(int|string $v): void { $this->victoires = max(0, (int) $v); }
    public function setDefaites(int|string $d): void { $this->defaites = max(0, (int) $d); }

    public function ajouterVictoire(): void { $this->victoires++; }
    public function ajouterDefaite(): void { $this->defaites++; }

    /** Remet le héros d'aplomb entre deux combats du tournoi. */
    public function soigner(): void
    {
        $this->pv = $this->pvMax;
    }

    // ─────────────────────────────── Affichage ───────────────────────────────

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getClasse()->emoji(), $this->name);
    }

    public function barreDeVie(): string
    {
        $cases = 10;
        $pleines = (int) round($this->pv / max(1, $this->pvMax) * $cases);

        return sprintf(
            '[%s%s] %d/%d PV',
            str_repeat('█', $pleines),
            str_repeat('░', $cases - $pleines),
            $this->pv,
            $this->pvMax
        );
    }
}
