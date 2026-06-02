<?php

namespace Modules\Products\app\Http\Controllers;

use App\Http\Controllers\Controller;

class ProductsController extends Controller
{
    public function __invoke(): string
    {
        return 'Products module';
    }
}
