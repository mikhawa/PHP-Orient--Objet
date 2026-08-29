<?php
/**
 * Corrigé 7.A — Le Choixpeau magique
 * match(true), match classique, UnhandledMatchError, comparaison avec switch.
 */

declare(strict_types=1);

class Choixpeau
{
    /**
     * ZÉRO `if` : uniquement des match.
     * match(true) évalue chaque condition et retient la PREMIÈRE vraie.
     */
    public function repartir(int $courage, int $ruse, int $sagesse, int $loyaute): string
    {
        $meilleur = max($courage, $ruse, $sagesse, $loyaute);

        return match (true) {
            // Cas particulier traité EN PREMIER : l'ordre compte.
            $meilleur === 0    => 'Cafétéria',
            $courage === $meilleur => 'GRYFFONDOR',
            $ruse === $meilleur    => 'SERPENTARD',
            $sagesse === $meilleur => 'SERDAIGLE',
            default                => 'POUFSOUFFLE',
        };
    }

    /** match classique : comparaison STRICTE (===) sur la valeur. */
    public function commentaire(string $maison): string
    {
        return match ($maison) {
            'GRYFFONDOR'  => 'Tu fonces d\'abord, tu debug ensuite.',
            'SERPENTARD'  => 'Tu as déjà lu la doc… et trouvé la faille.',
            'SERDAIGLE'   => 'Tu as commenté ton code. Spontanément.',
            'POUFSOUFFLE' => 'Tu aides tout le monde en salle de classe. ❤️',
            'Cafétéria'   => 'Hmm… rien du tout ? Va donc à la cafétéria.',
            // Pas de default : voir la démonstration plus bas.
        };
    }
}

// ─────────────────────────────── La répartition ───────────────────────────────

$choixpeau = new Choixpeau();

$stagiaires = [
    'Gaëlle'  => ['courage' => 8, 'ruse' => 3, 'sagesse' => 5, 'loyaute' => 4],
    'Hugo'    => ['courage' => 2, 'ruse' => 9, 'sagesse' => 6, 'loyaute' => 1],
    'Inès'    => ['courage' => 4, 'ruse' => 4, 'sagesse' => 9, 'loyaute' => 7],
    'Jonas'   => ['courage' => 3, 'ruse' => 2, 'sagesse' => 3, 'loyaute' => 9],
    'Fantôme' => ['courage' => 0, 'ruse' => 0, 'sagesse' => 0, 'loyaute' => 0],
];

echo '🎩 CÉRÉMONIE DE RÉPARTITION' . PHP_EOL . PHP_EOL;

foreach ($stagiaires as $nom => $s) {
    $maison = $choixpeau->repartir($s['courage'], $s['ruse'], $s['sagesse'], $s['loyaute']);

    echo "🎩 Le Choixpeau réfléchit pour {$nom}…" . PHP_EOL;
    printf(
        "   Courage %d · Ruse %d · Sagesse %d · Loyauté %d%s",
        $s['courage'], $s['ruse'], $s['sagesse'], $s['loyaute'], PHP_EOL
    );
    echo "   « $maison ! » — " . $choixpeau->commentaire($maison) . PHP_EOL . PHP_EOL;
}

// ────────────────────── 5. La même chose avec un switch ──────────────────────

function commentaireAvecSwitch(string $maison): string
{
    switch ($maison) {
        case 'GRYFFONDOR':
            $resultat = 'Tu fonces d\'abord, tu debug ensuite.';
            break;              // ← à ne PAS oublier
        case 'SERPENTARD':
            $resultat = 'Tu as déjà lu la doc… et trouvé la faille.';
            break;
        case 'SERDAIGLE':
            $resultat = 'Tu as commenté ton code. Spontanément.';
            break;
        case 'POUFSOUFFLE':
            $resultat = 'Tu aides tout le monde en salle de classe. ❤️';
            break;
        default:
            $resultat = 'Maison inconnue.';
    }

    return $resultat;           // ← une variable temporaire est nécessaire
}

echo '⚖️  LES 3 DIFFÉRENCES ENTRE match ET switch' . PHP_EOL;
echo <<<'TXT'

   1. VALEUR DE RETOUR
      match est une EXPRESSION : $x = match(...) { ... };
      switch est une INSTRUCTION : il faut une variable temporaire
      et un return/break, comme ci-dessus.

   2. COMPARAISON
      match compare avec === (STRICTE : type ET valeur).
      switch compare avec ==  (LÂCHE).
      Conséquence en PHP 7 : switch("1e2") entrait dans case "100" !
      PHP 8 a corrigé le pire de == pour les chaînes, mais la règle demeure :
      match ne vous piégera jamais.

   3. PAS DE break À OUBLIER
      Dans un switch, un break oublié fait « tomber » l'exécution dans le
      case suivant (fallthrough). C'est un classique du bug de production.
      match n'a aucun fallthrough : chaque branche est indépendante.

   BONUS 4. EXHAUSTIVITÉ
      Un match sans default lève UnhandledMatchError si aucun cas ne
      correspond. Un switch sans default ne fait… rien du tout, en silence.

TXT;

// ───────────── 6. Que se passe-t-il sans default ? ─────────────

echo PHP_EOL . '💥 6. LE match SANS default FACE À UNE VALEUR INCONNUE' . PHP_EOL;

try {
    echo $choixpeau->commentaire('POUDLARD EXPRESS');
} catch (\UnhandledMatchError $e) {
    echo '   ' . $e::class . ' : ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . '   Et la même chose avec le switch :' . PHP_EOL;
echo '   → ' . commentaireAvecSwitch('POUDLARD EXPRESS') . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    💡 CE COMPORTEMENT EST UNE FONCTIONNALITÉ, PAS UN DÉFAUT.

       Le jour où vous ajoutez une 5e maison, le match vous FORCE à traiter
       le cas — sinon UnhandledMatchError en fait crasher le programme, et
       vous le voyez immédiatement en développement.

       Un switch, lui, aurait silencieusement renvoyé sa valeur par défaut,
       et le bug serait parti en production sans un bruit. 🤫

       Utilisez donc `default` uniquement quand un fourre-tout est
       VRAIMENT le comportement voulu — pas par réflexe.
    TXT . PHP_EOL;
