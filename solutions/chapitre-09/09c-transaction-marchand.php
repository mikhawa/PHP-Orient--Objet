<?php
/**
 * Corrigé 9.C — La transaction du marchand
 * beginTransaction / commit / rollBack : tout ou rien.
 */

declare(strict_types=1);

class OrInsuffisantException extends RuntimeException
{
    public function __construct(public readonly int $manque)
    {
        parent::__construct("Il te manque $manque or !");
    }
}

class MarchandManager
{
    public function __construct(private PDO $pdo) {}

    /**
     * DEUX écritures qui doivent réussir ENSEMBLE ou échouer ENSEMBLE :
     *   1. débiter l'or du joueur ;
     *   2. ajouter l'objet à son inventaire.
     *
     * Sans transaction, une panne entre les deux laisserait un joueur
     * débité sans objet (ou pire : un objet gratuit).
     */
    public function acheter(int $joueurId, string $objet, int $prix, bool $saboter = false): void
    {
        $this->pdo->beginTransaction();

        try {
            // ── Vérification de l'or (verrouillage implicite dans la transaction)
            $stmt = $this->pdo->prepare('SELECT or_possede FROM joueur WHERE id = :id');
            $stmt->execute(['id' => $joueurId]);
            $or = $stmt->fetchColumn();

            if ($or === false) {
                throw new RuntimeException("Joueur #$joueurId introuvable.");
            }

            if ((int) $or < $prix) {
                throw new OrInsuffisantException($prix - (int) $or);
            }

            // ── 1re écriture : le débit
            $stmt = $this->pdo->prepare(
                'UPDATE joueur SET or_possede = or_possede - :prix WHERE id = :id'
            );
            $stmt->execute(['prix' => $prix, 'id' => $joueurId]);

            // ── Sabotage volontaire pour la démonstration du scénario 3
            if ($saboter) {
                $this->pdo->exec('INSERT INTO inventaire (colonne_inexistante) VALUES (1)');
            }

            // ── 2e écriture : l'objet
            $stmt = $this->pdo->prepare(
                'INSERT INTO inventaire (joueur_id, objet, prix)
                 VALUES (:joueur_id, :objet, :prix)'
            );
            $stmt->execute(['joueur_id' => $joueurId, 'objet' => $objet, 'prix' => $prix]);

            // Tout s'est bien passé : on valide DÉFINITIVEMENT.
            $this->pdo->commit();

        } catch (Throwable $e) {
            // Le moindre problème : on annule TOUT ce qui précède.
            $this->pdo->rollBack();

            // Le manager SIGNALE, il ne DÉCIDE pas à la place de l'appelant.
            // On relance : c'est au code appelant de choisir quoi afficher.
            throw $e;
        }
    }

    public function getOr(int $joueurId): int
    {
        $stmt = $this->pdo->prepare('SELECT or_possede FROM joueur WHERE id = :id');
        $stmt->execute(['id' => $joueurId]);

        return (int) $stmt->fetchColumn();
    }

    public function getInventaire(int $joueurId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT objet, prix FROM inventaire WHERE joueur_id = :id ORDER BY id'
        );
        $stmt->execute(['id' => $joueurId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ═══════════════════════════════ Le scénario ═══════════════════════════════

$pdo = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$pdo->exec('
    CREATE TABLE joueur (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        pseudo     VARCHAR(50) NOT NULL,
        or_possede INT NOT NULL DEFAULT 100
    )
');
$pdo->exec('
    CREATE TABLE inventaire (
        id        INTEGER PRIMARY KEY AUTOINCREMENT,
        joueur_id INT NOT NULL,
        objet     VARCHAR(100) NOT NULL,
        prix      INT NOT NULL
    )
');
$pdo->exec("INSERT INTO joueur (pseudo, or_possede) VALUES ('Aline', 100)");

$marchand = new MarchandManager($pdo);
$aline = 1;

echo '🏪 LA BOUTIQUE DU MARCHAND' . PHP_EOL . PHP_EOL;
printf("💰 Aline a %d or%s%s", $marchand->getOr($aline), PHP_EOL, PHP_EOL);

// ── Scénario 1 : achat réussi
echo '── Scénario 1 : achat normal ──' . PHP_EOL;

try {
    $marchand->acheter($aline, 'Épée en mousse', 30);
    printf("🛒 Achat d'une « Épée en mousse » (30 or) → ✅ Il reste %d or%s",
        $marchand->getOr($aline), PHP_EOL);
} catch (Throwable $e) {
    echo '⛔ ' . $e->getMessage() . PHP_EOL;
}

// ── Scénario 2 : trop cher
echo PHP_EOL . '── Scénario 2 : les yeux plus gros que la bourse ──' . PHP_EOL;

$orAvant = $marchand->getOr($aline);

try {
    $marchand->acheter($aline, 'Dragon apprivoisé', 5000);
} catch (OrInsuffisantException $e) {
    printf("🛒 Achat d'un « Dragon apprivoisé » (5000 or) → ⛔ %s%s", $e->getMessage(), PHP_EOL);
}

$orApres = $marchand->getOr($aline);
printf("💰 Vérification : Aline a toujours %d or %s%s",
    $orApres,
    $orAvant === $orApres ? '(transaction annulée ✅)' : '(⚠️ PROBLÈME !)',
    PHP_EOL
);

// ── Scénario 3 : panne au milieu de la transaction
echo PHP_EOL . '── Scénario 3 : erreur SQL EN PLEIN MILIEU ──' . PHP_EOL;

$orAvant = $marchand->getOr($aline);
printf("💰 Avant l'achat : %d or%s", $orAvant, PHP_EOL);

try {
    // saboter: true déclenche une erreur SQL APRÈS le débit, AVANT l'insert
    $marchand->acheter($aline, 'Potion de chance', 20, saboter: true);
} catch (PDOException $e) {
    echo '💥 Erreur SQL au milieu de la transaction :' . PHP_EOL;
    echo '   ' . strtok($e->getMessage(), "\n") . PHP_EOL;
}

$orApres = $marchand->getOr($aline);
printf("💰 Après le rollBack : %d or %s%s",
    $orApres,
    $orAvant === $orApres
        ? '→ le débit a bien été ANNULÉ ✅'
        : '→ ⚠️ l\'or a disparu, la transaction n\'a pas fonctionné !',
    PHP_EOL
);

// ── L'inventaire final
echo PHP_EOL . '🎒 INVENTAIRE FINAL D\'ALINE' . PHP_EOL;

foreach ($marchand->getInventaire($aline) as $ligne) {
    printf("   • %s (%d or)%s", $ligne['objet'], $ligne['prix'], PHP_EOL);
}

printf("   Or restant : %d%s", $marchand->getOr($aline), PHP_EOL);
echo '   👉 Aucune « Potion de chance » : ni objet, ni or débité. Cohérent. ✅' . PHP_EOL;

echo PHP_EOL . <<<'TXT'
    💡 CE QU'IL FAUT RETENIR SUR LES TRANSACTIONS

    QUAND EN UTILISER UNE ?
       Dès que DEUX écritures ou plus doivent rester cohérentes entre elles :
       virement bancaire (débit + crédit), commande (stock + facture),
       inscription (compte + profil + email de confirmation)…

    LE SQUELETTE, TOUJOURS LE MÊME :
       $pdo->beginTransaction();
       try {
           // ... les écritures ...
           $pdo->commit();
       } catch (Throwable $e) {
           $pdo->rollBack();
           throw $e;          // ← ne PAS avaler l'exception !
       }

    ⚠️ TROIS PIÈGES CLASSIQUES
       1. Oublier le rollBack() → la connexion reste bloquée en transaction
          ouverte, et les verrous ne sont jamais relâchés.
       2. Attraper l'exception sans la relancer → l'appelant croit que tout
          s'est bien passé. Le bug le plus sournois de la liste.
       3. En MySQL, les tables MyISAM ne gèrent PAS les transactions :
          il faut InnoDB (le défaut depuis MySQL 5.5, mais vérifiez).

    💡 Notez le catch (Throwable) : il attrape Exception ET Error. Même une
       erreur de programmation (TypeError…) doit déclencher le rollBack.
    TXT . PHP_EOL;
