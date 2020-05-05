<?php

namespace Tests;

use Tests\UseDatabaseTrait;
use Pimple\Container;
use PHPUnit\Framework\TestCase;
use Carbon\Carbon;
use App\Services\EloquentServiceProvider;
use App\Mailer\FakeMailer;
use App\Support\Facades\Facade;
use App\Cache\ArrayAdapter;

abstract class BaseTestCase extends TestCase
{
    /** 
     * Application
     * 
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->createApplication();

        $traits = array_flip(class_uses_recursive(static::class));
        
        if (isset($traits[UseDatabaseTrait::class])) {
            $this->setUpDatabase();
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $traits = array_flip(class_uses_recursive(static::class));
        
        if (isset($traits[UseDatabaseTrait::class])) {
            $this->tearDownDatabase();
        }
        
        unset($this->app);
        
        Carbon::setTestNow();

        parent::tearDown();
    }

    protected function createApplication()
    {
        $settings = require __DIR__ . '/../app/settings.php';

        $container = new Container(['settings'=> $settings]);

        $container['mail'] = function ($c) {
            return new FakeMailer;
        };

        $container['cache'] = function ($c) {
            return new ArrayAdapter;
        };

        $container->register(new EloquentServiceProvider);

        Facade::setFacadeContainer($container);

        $this->container = $container;
    }
}