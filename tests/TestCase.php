<?php

namespace QuixLabs\FilamentExtendable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use QuixLabs\FilamentExtendable\FilamentExtendableServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentExtendableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
