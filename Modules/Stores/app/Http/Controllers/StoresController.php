<?php

namespace Modules\Stores\app\Http\Controllers;

use App\Http\Controllers\Controller;

class StoresController extends Controller
{
    public function __invoke(): string
    {
        return 'Stores module';
    }
}
