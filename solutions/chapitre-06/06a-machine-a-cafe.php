<?php
/**
 * Corrigé 6.A — La machine à café
 * Exceptions personnalisées, try/catch/finally, ordre des vérifications.
 */

declare(strict_types=1);

// ─────────────────────── Les exceptions personnalisées ───────────────────────

class PlusDeCafeException extends Exception
{
    public function __construct()
    {
        parent::__construct('☕❌ Plus de café ! Le stock est vide.');
    }
}

class PlusDeGobeletException extends Exception
{
    public function __construct()
    {
        parent::__construct('😱 Plus de gobelets ! Revenez demain.');
    }
}

class MontantInsuffisantException extends Exception
{
    // Cette exception TRANSPORTE une donnée métier : le montant manquant.
    // C'est tout l'intérêt d'une classe dédiée par rapport à un simple message.
    public function __construct(public readonly float $manque)
    {
        parent::__construct(sprintf('💸 Il manque %.2f € !', $manque));
    }
}

// ─────────────────────────────── La machine ───────────────────────────────

class MachineACafe
{
    private const float PRIX = 0.50;

    public function __construct(
        private int $doses = 10,
        private int $gobelets = 5,
    ) {}

    /**
     * L'ORDRE DES VÉRIFICATIONS COMPTE :
     * on contrôle l'argent d'abord (le client peut corriger),
     * puis les ressources de la machine (le client n'y peut rien).
     */
    public function commander(float $montantInsere): string
    {
        if ($montantInsere < self::PRIX) {
            throw new MontantInsuffisantException(
                round(self::PRIX - $montantInsere, 2)
            );
        }

        if ($this->gobelets <= 0) {
            throw new PlusDeGobeletException();
        }

        if ($this->doses <= 0) {
            throw new PlusDeCafeException();
        }

        // Tout est bon : on décrémente et on sert.
        $this->doses--;
        $this->gobelets--;

        $monnaie = round($montantInsere - self::PRIX, 2);

        return $monnaie > 0
            ? sprintf('☕ Voici votre café ! (monnaie rendue : %.2f €)', $monnaie)
            : '☕ Voici votre café !';
    }

    public function stocks(): string
    {
        return sprintf('[stock : %d dose(s), %d gobelet(s)]', $this->doses, $this->gobelets);
    }
}

// ──────────────────────────── La file de collègues ────────────────────────────

$machine = new MachineACafe(doses: 10, gobelets: 3);

$file = [
    'Aline'  => 1.00,
    'Bob'    => 0.20,  // radin
    'Carla'  => 0.50,
    'Driss'  => 2.00,
    'Eddy'   => 0.45,  // il manque 5 centimes…
    'Fatima' => 0.50,
    'Gaël'   => 1.00,  // il n'y aura plus de gobelet
];

echo '☕ LA MACHINE À CAFÉ DE LA SALLE DE PAUSE' . PHP_EOL;
echo '   ' . $machine->stocks() . PHP_EOL . PHP_EOL;

$numero = 0;

foreach ($file as $nom => $montant) {
    $numero++;

    try {
        $resultat = $machine->commander($montant);
        printf("Client %d (%s) : %s%s", $numero, $nom, $resultat, PHP_EOL);

    } catch (MontantInsuffisantException $e) {
        // On attrape le type PRÉCIS en premier pour un traitement sur mesure :
        // on a accès à $e->manque, la donnée transportée par l'exception.
        printf("Client %d (%s) : %s%s", $numero, $nom, $e->getMessage(), PHP_EOL);

    } catch (PlusDeGobeletException | PlusDeCafeException $e) {
        // PHP 8.0 : plusieurs types dans un même catch.
        printf("Client %d (%s) : %s%s", $numero, $nom, $e->getMessage(), PHP_EOL);

    } finally {
        // finally s'exécute TOUJOURS : succès comme échec.
        // Idéal pour libérer une ressource, fermer un fichier, journaliser.
        echo '            ' . $machine->stocks() . PHP_EOL;
    }
}

echo PHP_EOL . <<<'TXT'
    💡 CE QU'IL FAUT RETENIR

       1. La machine ne fait JAMAIS planter le script. Chaque problème est
          signalé par une exception typée, que l'appelant traite comme il veut.

       2. Une exception personnalisée peut TRANSPORTER des données
          (ici $manque). Impossible avec un simple `return false`.

       3. L'ordre des catch va du PLUS PRÉCIS au PLUS GÉNÉRAL. Un
          `catch (Exception $e)` placé en premier attraperait tout et
          rendrait les suivants inaccessibles (PHP le signale d'ailleurs).

       4. finally s'exécute quoi qu'il arrive — même après un return
          dans le try. C'est le bloc du « nettoyage ».
    TXT . PHP_EOL;
