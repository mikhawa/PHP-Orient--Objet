<?php
/**
 * Corrigé 6.B — Le videur de la boîte de nuit
 * HIÉRARCHIE d'exceptions : un seul catch suffit pour attraper toute une famille.
 */

declare(strict_types=1);

// ─────────────────────────── La hiérarchie ───────────────────────────
//
//   Exception
//     └── RefusEntreeException          ← la famille
//           ├── TropJeuneException
//           ├── TenueNonReglementaireException
//           └── ListeNoireException

class RefusEntreeException extends Exception {}

class TropJeuneException extends RefusEntreeException
{
    public function __construct(public readonly int $age)
    {
        $manque = 18 - $age;
        parent::__construct(sprintf(
            '18 ans minimum, reviens dans %d an%s !',
            $manque,
            $manque > 1 ? 's' : ''
        ));
    }
}

class TenueNonReglementaireException extends RefusEntreeException
{
    public function __construct(public readonly string $delit)
    {
        parent::__construct("Les $delit ne passeront pas.");
    }
}

class ListeNoireException extends RefusEntreeException
{
    public function __construct()
    {
        parent::__construct("Toi, on t'a reconnu. C'est NON.");
    }
}

// ─────────────────────────────── Le videur ───────────────────────────────

class Videur
{
    public private(set) int $personnesControlees = 0;

    private const array TENUES_INTERDITES = [
        'claquettes-chaussettes',
        'training',
        'bob en jean',
    ];

    /**
     * @param array{nom: string, age: int, tenue: string, blackliste: bool} $client
     */
    public function controler(array $client): void
    {
        // Ordre de priorité : la liste noire d'abord, le reste ensuite.
        if ($client['blackliste']) {
            throw new ListeNoireException();
        }

        if ($client['age'] < 18) {
            throw new TropJeuneException($client['age']);
        }

        if (in_array($client['tenue'], self::TENUES_INTERDITES, strict: true)) {
            throw new TenueNonReglementaireException($client['tenue']);
        }
    }

    public function compter(): void
    {
        $this->personnesControlees++;
    }
}

// ─────────────────────────────── La file ───────────────────────────────

$videur = new Videur();

$file = [
    ['nom' => 'Aline', 'age' => 25, 'tenue' => 'robe',                    'blackliste' => false],
    ['nom' => 'Bob',   'age' => 15, 'tenue' => 'jean',                    'blackliste' => false],
    ['nom' => 'Carla', 'age' => 30, 'tenue' => 'claquettes-chaussettes',  'blackliste' => false],
    ['nom' => 'Driss', 'age' => 22, 'tenue' => 'costume',                 'blackliste' => false],
    ['nom' => 'Eddy',  'age' => 40, 'tenue' => 'smoking',                 'blackliste' => true],
    ['nom' => 'Fatou', 'age' => 17, 'tenue' => 'robe',                    'blackliste' => false],
];

echo '🕶️  CONTRÔLE À L\'ENTRÉE DU CLUB « LE NAMESPACE »' . PHP_EOL . PHP_EOL;

foreach ($file as $client) {
    try {
        $videur->controler($client);
        echo "✅ {$client['nom']} entre dans le club 🎉" . PHP_EOL;

    } catch (TropJeuneException $e) {
        // BONUS : traitement SPÉCIAL pour un type précis, placé AVANT
        // le catch général. L'ordre va du plus spécifique au plus large.
        echo "⛔ {$client['nom']} refoulé·e : {$e->getMessage()}" . PHP_EOL;
        echo "   …mais tiens, un ticket pour la soirée ado de samedi 🎟️" . PHP_EOL;

    } catch (RefusEntreeException $e) {
        // UN SEUL catch pour TenueNonReglementaire ET ListeNoire :
        // toutes deux héritent de RefusEntreeException.
        echo "⛔ {$client['nom']} refoulé·e : {$e->getMessage()}" . PHP_EOL;

    } finally {
        // Refusé ou pas, tout le monde est contrôlé.
        $videur->compter();
    }
}

echo PHP_EOL . "📊 Bilan de la soirée : {$videur->personnesControlees} personnes contrôlées." . PHP_EOL;

// ───────────────────────── Pourquoi ça marche ─────────────────────────

echo PHP_EOL . <<<'TXT'
    ❓ POURQUOI UN SEUL catch (RefusEntreeException) SUFFIT-IL ?

       Parce qu'un catch attrape le type demandé ET TOUS SES DESCENDANTS.
       TropJeuneException EST UNE RefusEntreeException (héritage), donc elle
       correspond. C'est le polymorphisme appliqué aux erreurs.

       Concrètement, cela veut dire que vous pouvez ajouter demain une
       ChaussuresBoueusesException extends RefusEntreeException : le code
       appelant n'a PAS besoin d'être modifié, il l'attrapera déjà.

    ⚠️  L'ORDRE DES catch : du plus précis au plus général.
       Si on inversait :

         catch (RefusEntreeException $e) { ... }   ← attrape tout
         catch (TropJeuneException $e)   { ... }   ← JAMAIS atteint

       PHP ne lève pas d'erreur, mais le second bloc est mort. Certains
       analyseurs statiques (PHPStan, Psalm) le détectent. Testez :
       inversez les deux catch ci-dessus et constatez que Bob et Fatou
       ne reçoivent plus leur ticket. 🎟️
    TXT . PHP_EOL;
