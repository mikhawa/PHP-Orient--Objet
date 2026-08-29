<?php
/**
 * Corrigé 7.C — Le GPS prudent
 * L'opérateur nullsafe ?-> (PHP 8.0) et l'opérateur de coalescence ?? (PHP 7)
 */

declare(strict_types=1);

class Pays
{
    public function __construct(public readonly string $nom) {}
}

class Ville
{
    public function __construct(
        public readonly string $nom,
        public readonly ?Pays $pays = null,
    ) {}
}

class Utilisateur
{
    public function __construct(
        public readonly string $pseudo,
        public readonly ?Ville $ville = null,
    ) {}
}

// ────────────────────────────── Les trois cas ──────────────────────────────

$utilisateurs = [
    new Utilisateur('Aline', new Ville('Charleroi', new Pays('Belgique'))),
    new Utilisateur('Bob',   new Ville('Nulle-Part')),   // ville sans pays
    new Utilisateur('Carla'),                            // pas de ville du tout
];

// ─────────────────────── 3. La version nullsafe : UNE ligne ───────────────────────

function localiser(Utilisateur $u): string
{
    // ?-> : si la partie gauche est null, TOUTE la chaîne vaut null
    //       (et rien n'est évalué à droite : c'est un court-circuit).
    // ??  : si le résultat est null, on prend la valeur de repli.
    $pays = $u->ville?->pays?->nom;

    return $pays === null
        ? "{$u->pseudo} est quelque part dans le multivers 🌀"
        : "{$u->pseudo} est en $pays";
}

echo '🧭 VERSION NULLSAFE (1 ligne, 0 if)' . PHP_EOL;
foreach ($utilisateurs as $u) {
    echo '   ' . localiser($u) . PHP_EOL;
}

// ─────────────────────── 4. La version sans nullsafe ───────────────────────

function localiserSansNullsafe(Utilisateur $u): string
{
    $pays = null;

    if ($u->ville !== null) {
        if ($u->ville->pays !== null) {
            $pays = $u->ville->pays->nom;
        }
    }

    if ($pays === null) {
        return "{$u->pseudo} est quelque part dans le multivers 🌀";
    }

    return "{$u->pseudo} est en $pays";
}

echo PHP_EOL . '🪜 VERSION AVEC if IMBRIQUÉS (7 lignes)' . PHP_EOL;
foreach ($utilisateurs as $u) {
    echo '   ' . localiserSansNullsafe($u) . PHP_EOL;
}

echo PHP_EOL . '📏 Comptage : 1 ligne utile contre 7. Et avec un niveau de' . PHP_EOL;
echo '   profondeur de plus, la version if en ferait 10, la nullsafe toujours 1.' . PHP_EOL;

// ─────────────────────── 5. Le piège sans le ? ───────────────────────

echo PHP_EOL . '💥 5. ET SANS L\'OPÉRATEUR NULLSAFE ?' . PHP_EOL;

// ⚠️ Nuance importante : lire une propriété sur null n'est PAS une Error,
// c'est un WARNING (et l'expression vaut null). Le programme CONTINUE.
// Un try/catch ne l'attraperait donc pas : il faut un gestionnaire d'erreurs.

set_error_handler(function (int $no, string $msg): bool {
    echo "   ⚠️  Warning : $msg" . PHP_EOL;
    return true;
});

$bob = $utilisateurs[1];
echo '   $bob->ville->pays->nom  (le pays est null)' . PHP_EOL;
$resultat = $bob->ville->pays->nom;
echo '   → résultat : ' . var_export($resultat, true) . PHP_EOL;

$carla = $utilisateurs[2];
echo PHP_EOL . '   $carla->ville->pays->nom  (la ville est null)' . PHP_EOL;
$resultat = $carla->ville->pays->nom;
echo '   → résultat : ' . var_export($resultat, true) . PHP_EOL;

restore_error_handler();

echo PHP_EOL . '   💡 Deux warnings en cascade pour Carla : PHP lit ->pays sur null' . PHP_EOL;
echo '      (warning 1, vaut null), puis ->nom sur ce null (warning 2).' . PHP_EOL;
echo '      Le script CONTINUE avec une valeur nulle silencieuse — exactement' . PHP_EOL;
echo '      le genre de bug que ?-> rend explicite et volontaire.' . PHP_EOL;

echo PHP_EOL . '   ⚡ En revanche, APPELER UNE MÉTHODE sur null est une Error fatale :' . PHP_EOL;
try {
    $carla->ville->getNom();
} catch (\Error $e) {
    echo '   → ' . $e::class . ' : ' . $e->getMessage() . PHP_EOL;
}

// ─────────────────────── Les pièges à connaître ───────────────────────

echo PHP_EOL . <<<'TXT'
    ⚠️  TROIS CHOSES À SAVOIR SUR ?->

    1. COURT-CIRCUIT : dans $a?->b()->c(), si $a est null, b() ET c() sont
       ignorées. Rien n'est évalué à droite. Pratique — mais attention si
       une de ces méthodes avait un effet de bord attendu !

    2. LECTURE SEULE : ?-> ne fonctionne PAS à gauche d'une affectation.
         $u->ville?->nom = 'Namur';   // Fatal error
       Logique : que voudrait dire « affecter à rien » ?

    3. ?-> N'EST PAS UN PANSEMENT UNIVERSEL. Si vous écrivez
       $a?->b?->c?->d?->e, demandez-vous plutôt pourquoi tant de choses
       peuvent être nulles. Souvent, le vrai correctif est en amont :
       une valeur par défaut, un objet « nul » (Null Object Pattern),
       ou un type non-nullable garanti par le constructeur.

    💡 ?? (coalescence) vs ?-> (nullsafe) : le premier remplace une valeur
       nulle, le second évite de PARCOURIR une valeur nulle. Ils se
       complètent, comme dans notre one-liner.
    TXT . PHP_EOL;
