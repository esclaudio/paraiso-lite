<?php

namespace Tests;

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

trait UseDatabaseTrait
{
    protected static $migrated = false;

    protected function setUpDatabase()
    {
        // Migrate if necessary

        if ( ! static::$migrated) {
            $app = new PhinxApplication();
            $app->doRun(new StringInput("migrate"), new NullOutput());
            static::$migrated = true;
        }

        // Start database transaction
        
        $connection = $this->container['db'];
        $dispatcher = $connection->getEventDispatcher();
        $connection->unsetEventDispatcher();
        $connection->beginTransaction();
        $connection->setEventDispatcher($dispatcher);
    }

    protected function tearDownDatabase()
    {
        // Rollback changes made to the database
        
        $connection = $this->container['db'];
        $dispatcher = $connection->getEventDispatcher();
        $connection->unsetEventDispatcher();
        $connection->rollback();
        $connection->setEventDispatcher($dispatcher);
        $connection->disconnect();
    }

    // protected function assertDatabaseHas($table, array $data)
    // {
    //     $builder = $this->app->getContainer()->get('db')->table($table);
    //     foreach ($data as $filed => $value) {
    //         $builder->where($filed, $value);
    //     }

    //     $this->assertTrue($builder->count() > 0,
    //         sprintf("$table table does not have %s under the column %s",
    //             $key = array_keys($data)[0],
    //             $data[$key]
    //         ));
    // }

    // protected function assertDatabaseDoesNotHave($table, array $data)
    // {
    //     $builder = $this->app->getContainer()->get('db')->table($table);
    //     foreach ($data as $filed => $value) {
    //         $builder->where($filed, $value);
    //     }

    //     $this->assertFalse($builder->count() > 0, 'Database has unwanted records in table ' . $table);
    // }
}