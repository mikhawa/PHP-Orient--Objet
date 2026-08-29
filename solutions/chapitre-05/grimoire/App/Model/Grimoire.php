<?php
namespace App\Model;

class Grimoire
{
    /** @var Sort[] */
    private array $sorts = [];

    public function ajouter(Sort $sort): void
    {
        $this->sorts[] = $sort;
    }

    public function nombreDeSorts(): int
    {
        return count($this->sorts);
    }

    /** @return Sort[] */
    public function getSorts(): array
    {
        return $this->sorts;
    }
}
