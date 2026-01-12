<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\UseCases\CollaboratorUseCase;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function index(): View
    {
        return view('welcome');
    }
}
