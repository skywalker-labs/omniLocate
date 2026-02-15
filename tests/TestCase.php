<?php

namespace Skywalker\Location\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Skywalker\Location\LocationServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [LocationServiceProvider::class];
    }
}

