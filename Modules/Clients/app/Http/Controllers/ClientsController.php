<?php

namespace Modules\Clients\app\Http\Controllers;

use App\Http\Controllers\Controller;

class ClientsController extends Controller
{
    public function __invoke(): string
    {
        return 'Clients module';
    }
}
