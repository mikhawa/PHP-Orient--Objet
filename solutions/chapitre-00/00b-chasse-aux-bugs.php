<?php
/**
 * Corrigé 0.B — La chasse aux bugs
 *
 * RÉPONSES AUX QUESTIONS
 *
 * 1. Le code affiche 0 (accompagné d'un Warning "Undefined array key").
 *    PHP n'arrête rien : une clé inexistante n'est qu'un avertissement, la
 *    valeur vaut null, et null * 2.50 vaut 0. Le total est donc FAUX sans que
 *    rien ne bloque l'exécution — le pire scénario possible.
 *
 * 2. Le bug : $ligne['qantite'] au lieu de $ligne['quantite'].
 *
 * 4. En objet, une faute de frappe sur un APPEL DE MÉTHODE devient une Error
 *    fatale : le programme s'arrête immédiatement au lieu de calculer faux.
 *
 * Deux autres avantages de la version objet :
 *    - le typage garantit qu'un prix est un float et une quantité un int
 *      (impossible d'y glisser "beaucoup" ou un tableau) ;
 *    - la logique (calculer un sous-total) vit AVEC les données : on ne peut
 *      ni l'oublier, ni la dupliquer dans trois fichiers différents.
 */

declare(strict_types=1);

// --------------------------------------------- 2. Version procédurale corrigée

$panier = [
    ['produit' => 'Gaufre',   'prix' => 2.50, 'quantite' => 4],
    ['produit' => 'Chocolat', 'prix' => 3.00, 'quantite' => 2],
];

function totalPanier(array $panier): float
{
    $total = 0.0;
    foreach ($panier as $ligne) {
        $total += $ligne['prix'] * $ligne['quantite']; // clé corrigée
    }
    return $total;
}

echo "Version procédurale corrigée : " . totalPanier($panier) . " €" . PHP_EOL;

// ------------------------------------------------------------ 3. Version objet

class LignePanier
{
    public function __construct(
        public readonly string $produit,
        public readonly float $prix,
        public readonly int $quantite,
    ) {}

    public function sousTotal(): float
    {
        return $this->prix * $this->quantite;
    }
}

class Panier
{
    /** @var LignePanier[] */
    private array $lignes = [];

    public function ajouter(LignePanier $ligne): void
    {
        $this->lignes[] = $ligne;
    }

    public function total(): float
    {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne->sousTotal();
        }
        return $total;
    }

    public function afficher(): void
    {
        foreach ($this->lignes as $ligne) {
            printf(
                "  %-10s %5.2f € x %d = %6.2f €%s",
                $ligne->produit, $ligne->prix, $ligne->quantite, $ligne->sousTotal(), PHP_EOL
            );
        }
        printf("  %-10s %20.2f €%s", 'TOTAL', $this->total(), PHP_EOL);
    }
}

$panierObjet = new Panier();
$panierObjet->ajouter(new LignePanier('Gaufre', 2.50, 4));
$panierObjet->ajouter(new LignePanier('Chocolat', 3.00, 2));

echo PHP_EOL . "Version objet :" . PHP_EOL;
$panierObjet->afficher();

// ------------------------------- 4. La même faute de frappe, version objet

echo PHP_EOL . "La faute de frappe, version objet :" . PHP_EOL;

$ligne = new LignePanier('Gaufre', 2.50, 4);

// Sur une MÉTHODE : Error FATALE. Le programme s'arrête, impossible de
// calculer un faux total sans s'en apercevoir. C'est ça, le vrai gain.
try {
    $ligne->sousTotl(); // faute de frappe volontaire
} catch (Error $e) {
    echo "  Méthode inexistante -> Error fatale : " . $e->getMessage() . PHP_EOL;
}

// Sur une PROPRIÉTÉ readonly typée : Warning, et la valeur vaut null.
// (Le @ masque le Warning pour que ce corrigé reste lisible ;
//  ne l'utilisez JAMAIS dans du vrai code !)
$valeur = @$ligne->qantite;
echo "  Propriété inexistante -> " . var_export($valeur, true)
    . " (un Warning est émis)" . PHP_EOL;
