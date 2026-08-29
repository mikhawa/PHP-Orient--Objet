<?php
namespace App\Manager;

use App\Enum\ClasseHero;
use App\Model\Guerrier;
use App\Model\Hero;
use App\Model\Mage;
use App\Model\Voleur;

class HeroManager
{
    public function __construct(private \PDO $pdo) {}

    public function add(Hero $hero): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO hero (name, classe, pv, force_attaque, victoires, defaites)
             VALUES (:name, :classe, :pv, :force_attaque, :victoires, :defaites)'
        );

        $stmt->execute([
            'name'          => $hero->getName(),
            'classe'        => $hero->getClasse()->value,
            'pv'            => $hero->getPvMax(),
            'force_attaque' => $hero->getForceAttaque(),
            'victoires'     => $hero->getVictoires(),
            'defaites'      => $hero->getDefaites(),
        ]);

        $hero->setId((int) $this->pdo->lastInsertId());
    }

    public function findById(int $id): ?Hero
    {
        $stmt = $this->pdo->prepare('SELECT * FROM hero WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row === false ? null : $this->instancier($row);
    }

    /** @return Hero[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM hero ORDER BY name');

        return array_map(
            fn(array $row) => $this->instancier($row),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    public function update(Hero $hero): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE hero
                SET name = :name, victoires = :victoires, defaites = :defaites
              WHERE id = :id'
        );

        $stmt->execute([
            'id'        => $hero->getId(),
            'name'      => $hero->getName(),
            'victoires' => $hero->getVictoires(),
            'defaites'  => $hero->getDefaites(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM hero WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** @return Hero[] */
    public function getClassement(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM hero ORDER BY victoires DESC, defaites ASC, name ASC'
        );

        return array_map(
            fn(array $row) => $this->instancier($row),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    /**
     * Les deux mises à jour réussissent ENSEMBLE ou pas du tout.
     * C'est le manager qui possède la connexion : c'est donc lui qui
     * porte la transaction. L'Arène n'a pas à connaître PDO.
     */
    public function enregistrerResultat(Hero $vainqueur, Hero $perdant): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->update($vainqueur);
            $this->update($perdant);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * La FABRIQUE : la colonne `classe` décide quelle classe PHP instancier.
     * C'est ici que le monde relationnel redevient un monde objet.
     */
    private function instancier(array $row): Hero
    {
        $classe = ClasseHero::tryFrom($row['classe'])
            ?? throw new \InvalidArgumentException("Classe inconnue : {$row['classe']}");

        return match ($classe) {
            ClasseHero::Guerrier => new Guerrier($row),
            ClasseHero::Mage     => new Mage($row),
            ClasseHero::Voleur   => new Voleur($row),
        };
    }
}
