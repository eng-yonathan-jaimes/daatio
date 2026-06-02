<?php

namespace Modules\Transactions\app\Http\Controllers;

use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    public function __invoke(): string
    {
        return 'Transactions module';
    }
}
