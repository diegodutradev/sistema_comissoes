<?php

namespace App\Http\Controllers;

use App\UseCases\InstallmentUseCase;
use Illuminate\View\View;

class InstallmentController
{
    protected InstallmentUseCase $installmentUseCase;

    public function __construct()
    {
        $this->installmentUseCase = new InstallmentUseCase();
    }

    public function markClientPaid($id): View
    {
        return view('welcome');
    }

    public function markCollaboratorPaid($id): View
    {
        return view('welcome');
    }
}
