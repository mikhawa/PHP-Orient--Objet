<?php
/**
 * Corrigé 3.D — Avant / après : la chasse aux chaînes magiques
 */

declare(strict_types=1);

// ═══════════════════════════════════ AVANT ═══════════════════════════════════

function degatsSort(string $element): int
{
    if ($element == 'feu') return 30;
    if ($element == 'glace') return 20;
    if ($element == 'foudre') return 25;
    return 0;
}

echo "❌ LA VERSION « CHAÎNES MAGIQUES »" . PHP_EOL;
printf("   degatsSort('feu')    = %d  ✅%s", degatsSort('feu'), PHP_EOL);
printf("   degatsSort('Feu')    = %d  ❌ majuscule → dégâts nuls, silencieusement%s", degatsSort('Feu'), PHP_EOL);
printf("   degatsSort('foudr')  = %d  ❌ typo → dégâts nuls, silencieusement%s", degatsSort('foudr'), PHP_EOL);
printf("   degatsSort('banane') = %d  ❌ n'importe quoi est accepté%s", degatsSort('banane'), PHP_EOL);

echo PHP_EOL . <<<'TXT'
    1. LES PROBLÈMES (il y en avait au moins trois) :
       a) Aucune validation : 'banane' est un élément parfaitement valide
          pour cette fonction, qui renvoie tranquillement 0.
       b) La casse et les fautes de frappe passent inaperçues : le sort
          fait 0 dégât et le joueur croit que c'est un équilibrage du jeu.
       c) Le == est une comparaison NON STRICTE : degatsSort('0') ferait des
          comparaisons hasardeuses en PHP 7 (heureusement corrigé en PHP 8).
       d) Bonus : impossible de savoir quels éléments existent sans lire le
          corps de la fonction. Aucune autocomplétion dans l'éditeur.
       e) Bonus : le "return 0" masque l'erreur au lieu de la signaler.
    TXT . PHP_EOL;

// ═══════════════════════════════════ APRÈS ═══════════════════════════════════

enum Element: string
{
    case Feu = 'feu';
    case Glace = 'glace';
    case Foudre = 'foudre';

    public function degats(): int
    {
        return match ($this) {
            Element::Feu    => 30,
            Element::Glace  => 20,
            Element::Foudre => 25,
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            Element::Feu    => '🔥',
            Element::Glace  => '❄️',
            Element::Foudre => '⚡',
        };
    }

    /** Le triangle des éléments : chacun craint le suivant. */
    public function faiblesse(): self
    {
        return match ($this) {
            Element::Feu    => Element::Glace,
            Element::Glace  => Element::Foudre,
            Element::Foudre => Element::Feu,
        };
    }
}

echo PHP_EOL . "✅ LA VERSION ENUM" . PHP_EOL;

foreach (Element::cases() as $element) {
    printf(
        "   %s %-7s %2d dégâts — craint %s %s%s",
        $element->emoji(),
        $element->name,
        $element->degats(),
        $element->faiblesse()->emoji(),
        $element->faiblesse()->name,
        PHP_EOL
    );
}

// ---------------------------------------- 3. La typo devient IMPOSSIBLE

echo PHP_EOL . "🔒 3. QUE SE PASSE-T-IL AVEC UNE TYPO ?" . PHP_EOL;

try {
    Element::from('foudr');
} catch (ValueError $e) {
    echo '   Element::from(\'foudr\') → ValueError : ' . $e->getMessage() . PHP_EOL;
}

echo "   → L'erreur est IMMÉDIATE et EXPLICITE, au lieu de 0 dégât silencieux." . PHP_EOL;

echo PHP_EOL . "   Et dans le code, on n'écrit même plus de chaîne :" . PHP_EOL;
echo '     $sort = Element::Foudre;   // autocomplété par l\'éditeur' . PHP_EOL;
echo '     Element::Foudr;            // ← erreur détectée à l\'écriture' . PHP_EOL;

// -------------------------------------------- 4. Le triangle complet

echo PHP_EOL . "🔺 4. LE TRIANGLE DES ÉLÉMENTS" . PHP_EOL;

foreach (Element::cases() as $a) {
    foreach (Element::cases() as $b) {
        if ($a === $b) {
            continue;
        }
        $verdict = $b->faiblesse() === $a
            ? "bat"
            : "perd contre";
        printf("   %s %-6s %-12s %s %s%s",
            $a->emoji(), $a->name, $verdict, $b->emoji(), $b->name, PHP_EOL);
    }
}

echo PHP_EOL . <<<'TXT'
    💡 BILAN : l'enum a remplacé 5 lignes de if par un type. Les gains :
       - impossible de passer une valeur invalide (le typage la refuse) ;
       - la liste des valeurs possibles est visible d'un coup d'œil ;
       - l'éditeur autocomplète et détecte les fautes AVANT l'exécution ;
       - le match sans default oblige à traiter tout nouveau cas ajouté ;
       - le comportement (dégâts, faiblesse) vit AVEC la donnée.
    TXT . PHP_EOL;
