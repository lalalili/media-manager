<?php

namespace Lalalili\MediaManager\Tests;

use Lalalili\MediaManager\MediaManagerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            MediaManagerServiceProvider::class,
        ];
    }
}
