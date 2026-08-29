<?php
/**
 * Corrigé 8.A — La bibliothèque interdite
 * Mapping table SQL → classe PHP, setters validateurs, exceptions.
 */

declare(strict_types=1);

class Livre
{
    // Propriétés typées, miroir des colonnes SQL.
    // ?int : l'id est inconnu tant que l'INSERT n'a pas eu lieu.
    private ?int $id = null;
    private string $titre = '';
    private string $auteur = '';
    private int $anneePublication = 0;
    private bool $estInterdit = false;     // TINYINT(1) côté SQL, bool côté PHP
    private ?float $noteMoyenne = null;    // DECIMAL nullable

    public function __construct(
        ?int $id,
        string $titre,
        string $auteur,
        int $anneePublication,
        bool|int|string $estInterdit = false,
        float|int|string|null $noteMoyenne = null,
    ) {
        // On passe TOUJOURS par les setters : c'est là que vit la validation.
        $this->setId($id);
        $this->setTitre($titre);
        $this->setAuteur($auteur);
        $this->setAnneePublication($anneePublication);
        $this->setEstInterdit($estInterdit);
        $this->setNoteMoyenne($noteMoyenne);
    }

    // ───────────────────────────── Getters ─────────────────────────────

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getAuteur(): string { return $this->auteur; }
    public function getAnneePublication(): int { return $this->anneePublication; }
    public function isEstInterdit(): bool { return $this->estInterdit; }
    public function getNoteMoyenne(): ?float { return $this->noteMoyenne; }

    // ─────────────────────── Setters : les gardes du corps ───────────────────────

    public function setId(?int $id): void
    {
        if ($id !== null && $id < 1) {
            throw new InvalidArgumentException("Un id vaut au minimum 1, reçu : $id");
        }
        $this->id = $id;
    }

    public function setTitre(string $titre): void
    {
        // strip_tags neutralise le HTML/JS injecté, trim enlève les espaces parasites.
        $propre = trim(strip_tags($titre));

        if ($propre === '') {
            throw new InvalidArgumentException('Le titre ne peut pas être vide.');
        }

        $this->titre = $propre;
    }

    public function setAuteur(string $auteur): void
    {
        $propre = trim(strip_tags($auteur));

        if ($propre === '') {
            throw new InvalidArgumentException("L'auteur ne peut pas être vide.");
        }

        $this->auteur = $propre;
    }

    public function setAnneePublication(int $annee): void
    {
        $anneeCourante = (int) date('Y');

        // 1450 : Gutenberg. Avant, pas d'imprimerie — donc pas de « livre » ici.
        if ($annee < 1450 || $annee > $anneeCourante) {
            throw new InvalidArgumentException(
                "Année impossible : $annee (attendu entre 1450 et $anneeCourante)"
            );
        }

        $this->anneePublication = $annee;
    }

    /**
     * SQL renvoie souvent "1"/"0" (chaînes) pour un TINYINT(1).
     * Le setter accepte tout et normalise en vrai bool.
     */
    public function setEstInterdit(bool|int|string $valeur): void
    {
        $this->estInterdit = (bool) (int) $valeur;
    }

    public function setNoteMoyenne(float|int|string|null $note): void
    {
        if ($note === null || $note === '') {
            $this->noteMoyenne = null;
            return;
        }

        // On borne au lieu de refuser : une note hors bornes est probablement
        // une erreur de saisie, pas une donnée à rejeter entièrement.
        $this->noteMoyenne = max(0.0, min(10.0, (float) $note));
    }

    // ─────────────────────────── Bonus : méthodes métier ───────────────────────────

    public function age(): int
    {
        return (int) date('Y') - $this->anneePublication;
    }

    public function estUnClassique(): bool
    {
        return $this->age() > 50;
    }

    public function __toString(): string
    {
        $emoji = $this->estInterdit ? '📕' : '📗';
        $ligne = sprintf('%s « %s » — %s (%d)', $emoji, $this->titre, $this->auteur, $this->anneePublication);

        if ($this->estInterdit) {
            $ligne .= ' ⛔ INTERDIT';
        }

        $ligne .= $this->noteMoyenne === null
            ? ' — pas encore noté'
            : sprintf(' — note %.1f/10', $this->noteMoyenne);

        if ($this->estUnClassique()) {
            $ligne .= ' 🏛️ classique';
        }

        return $ligne;
    }
}

// ─────────────────────── 4. Le test avec des lignes « SQL » ───────────────────────

$lignes = [
    ['id' => 1, 'titre' => '  Necronomicon  ', 'auteur' => 'Abdul Alhazred',
     'annee_publication' => 1650, 'est_interdit' => '1', 'note_moyenne' => '9.5'],

    ['id' => 2, 'titre' => 'Apprendre la POO', 'auteur' => 'M. Pitz',
     'annee_publication' => 2026, 'est_interdit' => 0, 'note_moyenne' => null],

    ['id' => 3, 'titre' => '<script>alert("piraté")</script>Le Grand Livre', 'auteur' => 'Hacker',
     'annee_publication' => 3025, 'est_interdit' => 0, 'note_moyenne' => 99],
];

echo '📚 LA BIBLIOTHÈQUE INTERDITE' . PHP_EOL . PHP_EOL;

foreach ($lignes as $i => $ligne) {
    try {
        $livre = new Livre(
            $ligne['id'],
            $ligne['titre'],
            $ligne['auteur'],
            $ligne['annee_publication'],
            $ligne['est_interdit'],
            $ligne['note_moyenne'],
        );

        echo '   ' . $livre . PHP_EOL;

    } catch (InvalidArgumentException $e) {
        printf("   ⚠️  Ligne %d rejetée : %s%s", $i + 1, $e->getMessage(), PHP_EOL);
    }
}

// ─────────────────────── Ce que les setters ont fait ───────────────────────

echo PHP_EOL . '🔍 CE QUE LES SETTERS ONT SILENCIEUSEMENT CORRIGÉ' . PHP_EOL . PHP_EOL;

$necronomicon = new Livre(1, '  Necronomicon  ', 'Abdul Alhazred', 1650, '1', '9.5');
printf("   Titre  : '  Necronomicon  ' → '%s'  (trim)%s", $necronomicon->getTitre(), PHP_EOL);
printf("   Interdit : '1' (string) → %s  (cast en bool)%s",
    var_export($necronomicon->isEstInterdit(), true), PHP_EOL);
printf("   Note   : '9.5' (string) → %s  (cast en float)%s",
    var_export($necronomicon->getNoteMoyenne(), true), PHP_EOL);

$injecte = new Livre(null, '<script>alert("xss")</script>Titre propre', 'Auteur', 2000, 0, 42);
printf("%s   XSS    : '<script>…</script>Titre propre' → '%s'  (strip_tags)%s",
    PHP_EOL, $injecte->getTitre(), PHP_EOL);
printf("   Note 42 → %.1f  (bornée à 10)%s", $injecte->getNoteMoyenne(), PHP_EOL);

echo PHP_EOL . <<<'TXT'
    💡 POURQUOI VALIDER DANS LES SETTERS PLUTÔT QU'AILLEURS ?

       Parce que c'est le SEUL point de passage. Que la donnée vienne d'un
       formulaire, d'un import CSV, d'une API ou d'un SELECT, elle traverse
       le setter. Une règle écrite une fois s'applique partout.

       Si la validation était dans le contrôleur, il faudrait la dupliquer
       dans chaque contrôleur — et l'oublier dans le script d'import de
       minuit, celui que personne ne relit. 🌙

    🔐 ATTENTION AU FAUX SENTIMENT DE SÉCURITÉ AVEC strip_tags()

       Regardez la sortie ci-dessus : '<script>alert("xss")</script>Titre propre'
       est devenu 'alert("xss")Titre propre'. Les BALISES ont disparu, mais le
       TEXTE est resté. strip_tags() nettoie la donnée, il ne protège PAS
       contre les failles XSS.

       La vraie protection XSS se fait À L'AFFICHAGE, pas au stockage :

           echo htmlspecialchars($livre->getTitre(), ENT_QUOTES, 'UTF-8');

       Règle d'or : on stocke la donnée telle que l'utilisateur l'a voulue,
       on l'échappe au moment de l'afficher, selon le contexte (HTML, URL,
       JSON, SQL…). Un même titre s'échappe différemment dans une page HTML
       et dans un attribut de balise.

       (Et pour SQL, l'échappement, c'est la requête PRÉPARÉE — chapitre 9.)

    ⚖️  REJETER (exception) ou CORRIGER (borner) ?
       - Une donnée ABERRANTE qui trahit un bug → exception (année 3025).
       - Une donnée IMPRÉCISE mais exploitable → correction silencieuse
         (espaces, casse, note légèrement hors bornes).
       Dans le doute, l'exception est plus sûre : mieux vaut un plantage
       visible qu'une donnée fausse en base.
    TXT . PHP_EOL;
