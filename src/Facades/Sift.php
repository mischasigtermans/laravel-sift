<?php

namespace MischaSigtermans\Sift\Facades;

use Illuminate\Support\Facades\Facade;

class Sift extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MischaSigtermans\Sift\Sift::class;
    }
}
