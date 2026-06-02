<?php

namespace Modules\Users\app\Http\Controllers;

use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function __invoke(): string
    {
        return 'Users module';
    }
}
