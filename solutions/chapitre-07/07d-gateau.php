<?php
/**
 * Corrigé 7.D — L'usine à arguments nommés
 * Arguments nommés (8.0), new dans les initialiseurs (8.1).
 */

declare(strict_types=1);

class Gateau
{
    public function __construct(
        private string $base = 'génoise',
        private string $garniture = 'chantilly',
        private string $nappage = 'chocolat',
        private int $bougies = 0,
        private string $messageEcrit = '',
        private bool $sansGluten = false,
    ) {}

    public function __toString(): string
    {
        $description = "🎂 Un gâteau {$this->base}, garni de {$this->garniture}, "
            . "nappé de {$this->nappage}";

        if ($this->bougies > 0) {
            $description .= ", avec {$this->bougies} bougie"
                . ($this->bougies > 1 ? 's' : '');
        }

        if ($this->messageEcrit !== '') {
            $description .= ", où l'on peut lire « {$this->messageEcrit} »";
        }

        if ($this->sansGluten) {
            $description .= ' (sans gluten 🌾❌)';
        }

        return $description . '.';
    }
}

// ────────────────────── 3. Quatre commandes, arguments nommés ──────────────────────

echo '🧁 LES COMMANDES DU JOUR' . PHP_EOL . PHP_EOL;

$commandes = [
    // Le gâteau standard : aucun argument.
    new Gateau(),

    // On ne précise QUE ce qui change — et le 6e paramètre sans passer
    // par les cinq premiers : impossible sans arguments nommés !
    new Gateau(sansGluten: true),

    // Dans l'ordre qu'on veut.
    new Gateau(garniture: 'crème pâtissière', base: 'pâte sablée', nappage: 'fruits rouges'),

    // L'anniversaire.
    new Gateau(
        bougies: 30,
        messageEcrit: 'Joyeux anniversaire Aline !',
        garniture: 'mousse au chocolat',
    ),
];

foreach ($commandes as $i => $gateau) {
    echo '   ' . ($i + 1) . '. ' . $gateau . PHP_EOL;
}

// ────────────────────── 4. new dans un initialiseur (PHP 8.1) ──────────────────────

class Commande
{
    public function __construct(
        // PHP 8.1 : on peut instancier un objet comme valeur par défaut.
        // Avant, il fallait accepter null puis créer l'objet dans le corps.
        private Gateau $gateau = new Gateau(),
        private string $client = 'Client anonyme',
    ) {}

    public function ticket(): string
    {
        return "🧾 {$this->client} : {$this->gateau}";
    }
}

echo PHP_EOL . '🧾 LES TICKETS' . PHP_EOL . PHP_EOL;

// Aucun gâteau précisé → le gâteau standard est créé automatiquement
echo '   ' . (new Commande(client: 'Passant pressé'))->ticket() . PHP_EOL;

echo '   ' . (new Commande(
    gateau: new Gateau(base: 'meringue', bougies: 1),
    client: 'Bébé Lucas',
))->ticket() . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    ⚠️  LIMITE DE `new` DANS UN INITIALISEUR : la valeur par défaut ne peut
       PAS dépendre d'autre chose (pas d'appel de fonction non constante,
       pas de $this). Elle est évaluée à CHAQUE appel, pas une fois pour
       toutes — chaque Commande reçoit donc son propre Gateau, pas un
       objet partagé. Ouf ! 😅

TXT;

// ────────────────────── 5. La règle des arguments positionnels ──────────────────────

echo '💥 5. UN ARGUMENT POSITIONNEL APRÈS UN ARGUMENT NOMMÉ ?' . PHP_EOL . PHP_EOL;

$code = <<<'CODE'
<?php
class Gateau {
    public function __construct(
        private string $base = 'génoise',
        private string $garniture = 'chantilly',
    ) {}
}
$g = new Gateau(garniture: 'fraise', 'génoise');
CODE;

$fichier = tempnam(sys_get_temp_dir(), 'nomme') . '.php';
file_put_contents($fichier, $code);
$sortie = shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($fichier) . ' 2>&1');
unlink($fichier);

echo '   ' . preg_replace('/ in \/.*$/m', '', trim(strtok((string) $sortie, "\n"))) . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    📏 LA RÈGLE : les arguments POSITIONNELS d'abord, les NOMMÉS ensuite.

         new Gateau('génoise', garniture: 'fraise');   ✅
         new Gateau(garniture: 'fraise', 'génoise');   ❌

       C'est logique : une fois qu'on a nommé un argument, PHP ne sait plus
       à quelle position rattacher les suivants. Le 'génoise' de l'exemple
       raté serait-il le 1er paramètre ? Le 3e ? Ambigu → interdit.

    💡 BONUS : les arguments nommés fonctionnent aussi avec les fonctions
       natives, et rendent le code bien plus lisible :

         str_contains(haystack: $texte, needle: 'php')
         array_slice($tab, offset: 2, length: 3, preserve_keys: true)
         json_encode($data, flags: JSON_PRETTY_PRINT)
         in_array($x, $tab, strict: true)     ← surtout celui-ci !
    TXT . PHP_EOL;
