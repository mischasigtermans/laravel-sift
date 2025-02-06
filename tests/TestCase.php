<?php

namespace MischaSigtermans\Sift\Tests;

use MischaSigtermans\Sift\Facades\Sift;
use MischaSigtermans\Sift\SiftServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SiftServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Sift' => Sift::class,
        ];
    }
}
