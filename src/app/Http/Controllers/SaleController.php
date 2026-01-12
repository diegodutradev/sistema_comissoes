<?php

namespace App\Http\Controllers;

use App\UseCases\SaleUseCase;
use Illuminate\View\View;

class SaleController
{
    protected SaleUseCase $saleUseCase;

    public function __construct()
    {
        $this->saleUseCase = new SaleUseCase();
    }

    public function new(): View
    {
        return view('welcome');
    }
}
