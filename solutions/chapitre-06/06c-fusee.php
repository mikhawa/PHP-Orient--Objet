<?php
/**
 * Corrigé 6.C — La fusée à étages
 * Attraper, envelopper, relancer : le paramètre $previous.
 */

declare(strict_types=1);

class MoteurEnPanneException extends RuntimeException {}
class DecollageAnnuleException extends RuntimeException {}

class Moteur
{
    public function __construct(private int $numero) {}

    public function allumer(): void
    {
        // Une fois sur deux, la technique décide de faire des siennes.
        if (random_int(0, 1) === 0) {
            throw new MoteurEnPanneException(
                "Injecteur {$this->numero} bouché (pression : 0.3 bar au lieu de 4.2)"
            );
        }

        echo "   ✅ Moteur {$this->numero} allumé." . PHP_EOL;
    }
}

class Fusee
{
    /** @var Moteur[] */
    private array $moteurs;

    public function __construct(public readonly string $nom, int $nbMoteurs = 3)
    {
        for ($i = 1; $i <= $nbMoteurs; $i++) {
            $this->moteurs[] = new Moteur($i);
        }
    }

    public function decoller(): void
    {
        echo "🚀 Séquence de décollage de « {$this->nom} »" . PHP_EOL;

        try {
            foreach ($this->moteurs as $moteur) {
                $moteur->allumer();
            }

            echo "   🔥 DÉCOLLAGE ! Tous les moteurs sont nominaux." . PHP_EOL;

        } catch (MoteurEnPanneException $e) {
            // On ne laisse PAS fuiter le détail technique vers le grand public.
            // On enveloppe l'exception d'origine dans une exception de plus
            // haut niveau, en la conservant grâce à `previous`.
            throw new DecollageAnnuleException(
                'Décollage annulé, restez calmes.',
                previous: $e            // argument nommé : plus lisible que 0, $e
            );
        }
    }
}

// ────────────────────────────── Le script principal ──────────────────────────────

// On rejoue jusqu'à obtenir les deux issues, pour la démonstration.
$succes = $echec = false;
$tentative = 0;

while ((!$succes || !$echec) && $tentative < 50) {
    $tentative++;

    try {
        (new Fusee('Ariane ' . $tentative))->decoller();
        $succes = true;

    } catch (DecollageAnnuleException $e) {
        $echec = true;

        // Le message « grand public »
        echo '   🚨 ' . $e->getMessage() . PHP_EOL;

        // La cause technique, réservée aux ingénieurs.
        // getPrevious() peut renvoyer null → l'opérateur nullsafe ?-> est parfait.
        echo '      Cause technique : ' . $e->getPrevious()?->getMessage() . PHP_EOL;
    }

    echo PHP_EOL;
}

// ───────────────────────── La chaîne complète ─────────────────────────

echo "🔗 LA CHAÎNE DES CAUSES (utile en journalisation)" . PHP_EOL;

try {
    (new Fusee('Démonstration', nbMoteurs: 1))->decoller();
    echo "   (ce lancement-ci a réussi, relancez le script !)" . PHP_EOL;
} catch (DecollageAnnuleException $e) {
    $niveau = 0;
    $courante = $e;

    // On remonte toute la pile des causes.
    while ($courante !== null) {
        printf(
            "   %s%s : %s%s",
            str_repeat('   ', $niveau),
            $courante::class,
            $courante->getMessage(),
            PHP_EOL
        );
        $courante = $courante->getPrevious();
        $niveau++;
    }
}

echo PHP_EOL . <<<'TXT'
    ❓ POURQUOI DEUX MESSAGES DIFFÉRENTS ?

       C'est le principe de SÉPARATION DES NIVEAUX D'ABSTRACTION. Chaque
       couche parle le langage de son interlocuteur :
         - le Moteur parle technique (« injecteur 3 bouché ») ;
         - la Fusée parle mission (« décollage annulé ») ;
         - le public entend un message rassurant et actionnable.

       On garde la cause d'origine avec `previous` : rien n'est perdu, le
       journal technique contient toute la chaîne.

    🌍 OÙ RETROUVE-T-ON ÇA DANS UNE VRAIE APPLICATION WEB ?

       Exactement au même endroit :
         - PDOException : « SQLSTATE[42S02] Table 'users' doesn't exist »
           → l'utilisateur voit : « Une erreur est survenue, réessayez. »
         - le détail part dans les logs (error_log, Monolog, Sentry) ;
         - l'utilisateur ne voit JAMAIS le message SQL brut.

       Afficher un message d'erreur technique en production est d'ailleurs
       une faille de sécurité : nom des tables, chemins des fichiers,
       version du serveur… tout cela renseigne un attaquant. 🔐
    TXT . PHP_EOL;
