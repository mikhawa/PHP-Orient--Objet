<?php
/**
 * Corrigé 6.1 à 6.5 — Les exceptions, étape par étape
 */

declare(strict_types=1);

// ─────────────────── 6.3 : une exception personnalisée ───────────────────
// Elle est VIDE : elle hérite de tout ce que fait Exception.
// Son seul intérêt (et il est énorme) : porter un NOM parlant, qu'on peut
// attraper spécifiquement.
class DivisionParZeroException extends Exception
{
}

// ─────────────────── 6.5 : une exception qui transporte une donnée ───────────────────
class MontantInsuffisantException extends Exception
{
    public function __construct(public readonly float $manque)
    {
        // On appelle le constructeur du parent pour définir le message.
        parent::__construct('💸 Il manque ' . number_format($manque, 2) . ' € !');
    }
}

function diviser(int $a, int $b): float
{
    if ($b === 0) {
        throw new DivisionParZeroException('Division par zéro impossible !');
    }

    return $a / $b;
}

// ═══════════════════════ 6.1 et 6.2 ═══════════════════════

echo '── 6.1 : lancer et attraper ──' . PHP_EOL;

echo diviser(10, 2) . PHP_EOL;

try {
    echo diviser(10, 0) . PHP_EOL;
} catch (DivisionParZeroException $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;

    // 6.2 : une exception est un OBJET, avec des méthodes utiles
    echo '   Ligne : ' . $e->getLine() . PHP_EOL;
}

echo 'Le programme continue !' . PHP_EOL;

// ═══════════════════════ 6.3 : la question ═══════════════════════

echo PHP_EOL . '── 6.3 : catch (Exception) attrape-t-il notre exception ? ──' . PHP_EOL;

try {
    diviser(10, 0);
} catch (Exception $e) {
    echo '✅ OUI : ' . $e::class . ' EST UNE Exception (héritage).' . PHP_EOL;
    echo '   Un catch attrape le type demandé ET tous ses descendants.' . PHP_EOL;
}

// ═══════════════════════ 6.4 et 6.5 : la machine à café ═══════════════════════

class MachineACafe
{
    private int $gobelets = 3;

    private const float PRIX = 0.50;

    public function servir(): string
    {
        if ($this->gobelets === 0) {
            throw new Exception('😱 Plus de gobelets !');
        }

        $this->gobelets--;

        return '☕ Voici votre café !';
    }

    // 6.5
    public function commander(float $argent): string
    {
        if ($argent < self::PRIX) {
            throw new MontantInsuffisantException(self::PRIX - $argent);
        }

        return $this->servir();
    }
}

echo PHP_EOL . '── 6.4 : la file de clients ──' . PHP_EOL;

$machine = new MachineACafe();

for ($client = 1; $client <= 5; $client++) {
    try {
        echo "Client $client : " . $machine->servir() . PHP_EOL;
    } catch (Exception $e) {
        echo "Client $client : " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . '── 6.5 : l\'exception qui transporte une donnée ──' . PHP_EOL;

$machine2 = new MachineACafe();

foreach ([1.00, 0.20, 0.45] as $numero => $argent) {
    try {
        echo 'Client ' . ($numero + 1) . ' : ' . $machine2->commander($argent) . PHP_EOL;
    } catch (MontantInsuffisantException $e) {
        echo 'Client ' . ($numero + 1) . ' : ' . $e->getMessage() . PHP_EOL;
        // On lit la donnée transportée par l'exception :
        echo '            (il manque exactement ' . $e->manque . ' €)' . PHP_EOL;
    }
}

echo PHP_EOL . <<<'TXT'
💡 CE QUE VOUS AVEZ APPRIS

   throw               lancer une exception (le programme s'arrête là)
   try { } catch { }   attraper l'exception et continuer
   getMessage()        le texte de l'exception
   getLine()           la ligne où elle a été lancée
   extends Exception   créer sa propre exception (elle peut être vide !)
   un catch attrape le type demandé ET tous ses descendants

   👉 POURQUOI CRÉER SES PROPRES EXCEPTIONS ?
      Pour pouvoir réagir DIFFÉREMMENT selon le problème : un catch pour
      le manque d'argent (on rend la monnaie), un autre pour la panne
      (on appelle le technicien). Avec une Exception générique, il
      faudrait lire le message texte pour deviner — fragile et pénible.

TXT;
