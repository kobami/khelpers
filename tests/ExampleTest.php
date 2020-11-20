<?php

namespace Kobami\Khelpers\Tests;

use Orchestra\Testbench\TestCase;
use Kobami\Khelpers\KhelpersServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [KhelpersServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
