<?php

namespace App\Support\Dashboard;

use Slim\Views\Twig;
use PDO;

abstract class Widget
{
    /**
     * PDO
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * Twig
     *
     * @var \Slim\Views\Twig
     */
    protected $twig;

    public function __construct(PDO $pdo, Twig $twig)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }

    public abstract function title(): string;
    protected abstract function view(): string;
    protected abstract function data(): array;

    public function render(): string
    {
        $data = $this->data();
        $view = $this->view();

        return $this->twig->fetch($view, compact('data'));
    }

    protected function fetch(string $sql): array
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}