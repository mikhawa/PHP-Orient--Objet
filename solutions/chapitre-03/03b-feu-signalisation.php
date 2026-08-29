<?php
/**
 * Corrigé 3.B — Le feu de signalisation
 * Enum PUR (sans valeur adossée) + match qui renvoie un autre cas de l'enum.
 */

declare(strict_types=1);

enum Feu
{
    case Rouge;
    case Orange;
    case Vert;

    /** Un match peut parfaitement renvoyer un autre cas de l'enum. */
    public function suivant(): self
    {
        return match ($this) {
            Feu::Vert   => Feu::Orange,
            Feu::Orange => Feu::Rouge,
            Feu::Rouge  => Feu::Vert,
        };
    }

    public function duree(): int
    {
        return match ($this) {
            Feu::Vert   => 30,
            Feu::Orange => 5,
            Feu::Rouge  => 25,
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            Feu::Vert   => '🟢',
            Feu::Orange => '🟠',
            Feu::Rouge  => '🔴',
        };
    }

    public function libelle(): string
    {
        return match ($this) {
            Feu::Vert   => 'Vert',
            Feu::Orange => 'Orange',
            Feu::Rouge  => 'Rouge',
        };
        // Variante : $this->name renvoie déjà 'Vert', 'Orange', 'Rouge'.
    }

    /** Bonus : le taxi pressé passe aussi à l'orange (à ne pas reproduire !). */
    public function peutPasser(bool $estUnTaxiPresse = false): bool
    {
        return match ($this) {
            Feu::Vert   => true,
            Feu::Orange => $estUnTaxiPresse,
            Feu::Rouge  => false,
        };
    }
}

// ------------------------------------------------------------ 2 cycles complets

echo "🚦 SIMULATION : 2 cycles complets" . PHP_EOL . PHP_EOL;

$feu = Feu::Vert;
$dureeTotale = 0;

for ($i = 0; $i < 6; $i++) { // 2 cycles × 3 états
    printf("%s %s pendant %d s%s", $feu->emoji(), $feu->libelle(), $feu->duree(), PHP_EOL);
    $dureeTotale += $feu->duree();
    $feu = $feu->suivant();
}

echo PHP_EOL . "Durée totale de la simulation : {$dureeTotale} s" . PHP_EOL;

// ------------------------------------------------------- Le taxi pressé 🚕

echo PHP_EOL . "🚕 Qui peut passer ?" . PHP_EOL;
printf("%-10s %-22s %s%s", '', 'Conducteur normal', 'Taxi pressé', PHP_EOL);

foreach (Feu::cases() as $etat) {
    printf(
        "%s %-8s %-22s %s%s",
        $etat->emoji(),
        $etat->libelle(),
        $etat->peutPasser() ? '✅ passe' : '⛔ s\'arrête',
        $etat->peutPasser(estUnTaxiPresse: true) ? '✅ passe' : '⛔ s\'arrête',
        PHP_EOL
    );
}

// ------------------------------------------------------- Enum pur vs adossé

echo PHP_EOL . <<<'TXT'
    💡 ENUM PUR ou ENUM ADOSSÉ (backed) ?

       Ici, un enum PUR suffit : les états du feu n'ont pas besoin d'être
       stockés en base de données ni transmis dans une URL. Les cas existent
       uniquement en mémoire, et ->name donne déjà leur nom.

       Un enum ADOSSÉ (enum Feu: string) devient nécessaire dès qu'il faut
       traverser une frontière : colonne SQL, JSON, formulaire HTML, API.
       On utilise alors ->value pour sortir et ::from() / ::tryFrom() pour
       rentrer. C'est ce que nous faisons au chapitre 8 avec les créatures.
    TXT . PHP_EOL;

// Démonstration : ->name existe sur tous les enums, ->value seulement sur les adossés
echo PHP_EOL . 'Feu::Vert->name = ' . Feu::Vert->name . PHP_EOL;
echo 'Feu::Vert->value → n\'existe pas sur un enum pur (Error si on essaie)' . PHP_EOL;
