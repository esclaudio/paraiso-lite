<?php

namespace App\Support\Excel\Exporter;

use PDO;
use Illuminate\Support\Collection;

abstract class PDOExporter extends Exporter
{
    /**
     * PDO
     *
     * @var \Pdo
     */
    protected $pdo;

    abstract protected function getQuery(): string;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function getCollection(): Collection
    {
        $query = $this->getQuery();
        
        $stm = $this->pdo->prepare($query);
        $stm->execute();

        $items = $stm->fetchAll(PDO::FETCH_ASSOC);

        return new Collection($items);
    }
}