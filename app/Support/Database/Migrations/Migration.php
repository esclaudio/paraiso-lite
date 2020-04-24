<?php

namespace App\Support\Database\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * Schema
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Connection
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * Driver
     *
     * @var string
     */
    protected $driver;

    public function init()
    {
        $this->schema = (new Capsule)->schema();
        $this->connection = $this->schema->getConnection();
        $this->driver = $this->connection->getDriverName();
    }
}
