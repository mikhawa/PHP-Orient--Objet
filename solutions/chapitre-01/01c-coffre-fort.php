<?php
/**
 * Corrigé 1.C — Le coffre-fort
 * Encapsulation : l'état interne est PROTÉGÉ, seules les méthodes le changent.
 */

declare(strict_types=1);

class CoffreFort
{
    private array $contenu = [];
    private bool $verrouille = true;
    private int $echecs = 0;
    private bool $bloqueAJamais = false;

    private const int MAX_ESSAIS = 3;

    public function __construct(private string $code) {}

    public function deverrouiller(string $tentative): bool
    {
        if ($this->bloqueAJamais) {
            echo "🔒💀 Coffre bloqué définitivement. Appelez un serrurier." . PHP_EOL;
            return false;
        }

        // hash_equals : comparaison à temps constant, la bonne pratique
        // pour comparer un secret (évite les attaques temporelles).
        if (hash_equals($this->code, $tentative)) {
            $this->verrouille = false;
            $this->echecs = 0; // le compteur repart à zéro
            echo "🔓 Clic ! Coffre déverrouillé." . PHP_EOL;
            return true;
        }

        $this->echecs++;
        $restants = self::MAX_ESSAIS - $this->echecs;

        if ($restants <= 0) {
            $this->bloqueAJamais = true;
            echo "❌ Code faux. 🚨 TROIS ÉCHECS : le coffre se bloque à jamais !" . PHP_EOL;
            return false;
        }

        echo "❌ Code faux. Il reste $restants essai(s)." . PHP_EOL;
        return false;
    }

    public function deposer(string $objet): void
    {
        if (!$this->estAccessible()) {
            return;
        }
        $this->contenu[] = $objet;
        echo "📥 « $objet » déposé dans le coffre." . PHP_EOL;
    }

    public function ouvrir(): array
    {
        if (!$this->estAccessible()) {
            return [];
        }
        echo "📤 Contenu du coffre : " . implode(', ', $this->contenu) . PHP_EOL;
        return $this->contenu;
    }

    public function verrouiller(): void
    {
        $this->verrouille = true;
        echo "🔐 Coffre refermé." . PHP_EOL;
    }

    private function estAccessible(): bool
    {
        if ($this->bloqueAJamais) {
            echo "🔒💀 Coffre bloqué définitivement." . PHP_EOL;
            return false;
        }
        if ($this->verrouille) {
            echo "🔒 Le coffre est verrouillé, déverrouillez-le d'abord." . PHP_EOL;
            return false;
        }
        return true;
    }
}

// ------------------------------------------------------------- Le scénario 🏴‍☠️

$coffre = new CoffreFort('1789');

echo "--- Le propriétaire range ses trésors ---" . PHP_EOL;
$coffre->deverrouiller('1789');
$coffre->deposer("Lingot d'or");
$coffre->deposer('Recette secrète des gaufres');
$coffre->verrouiller();

echo PHP_EOL . "--- La nuit, un pirate s'introduit 🏴‍☠️ ---" . PHP_EOL;
$coffre->ouvrir();               // verrouillé : rien à voir
$coffre->deverrouiller('0000');
$coffre->deverrouiller('1234');
$coffre->deverrouiller('4321');

echo PHP_EOL . "😱 Il trouve enfin le bon code sur un post-it… trop tard !" . PHP_EOL;
$coffre->deverrouiller('1789');
$coffre->ouvrir();

echo PHP_EOL . "--- Le lendemain, le propriétaire revient ---" . PHP_EOL;
$coffre->deverrouiller('1789'); // même lui est bloqué : c'est le prix de la sécurité

echo PHP_EOL . <<<'TXT'
    💡 À retenir : $verrouille, $echecs et $code sont PRIVÉS. Le pirate ne
       peut pas écrire $coffre->verrouille = false; ni lire le code. Tout
       passe par les méthodes publiques, qui font respecter les règles.

       Notez aussi le nowdoc <<<'TXT' (avec quotes) au lieu du heredoc
       <<<TXT : sans les quotes, PHP tenterait d'interpoler $verrouille !
    TXT . PHP_EOL;
