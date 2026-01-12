<?php

namespace App\Http\Controllers;

use App\DTO\CollaboratorDTO;
use Illuminate\Http\Request;
use App\Repository\CollaboratorRepository;
use App\UseCases\CollaboratorUseCase;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CollaboratorController extends Controller
{
    protected CollaboratorUseCase $collaboratorUseCase;

    public function __construct()
    {
        $this->collaboratorUseCase = new CollaboratorUseCase(new CollaboratorRepository());
    }

    public function index(): View
    {
        $collaborators = $this->collaboratorUseCase->getAllCollaborators();
        return view('collaborators', ['collaborators' => $collaborators]);
    }

    public function create(Request $req): RedirectResponse
    {
        // dd($req->post());
        $collaboratorFields = $req->post();

        $collaboratorDTO = new CollaboratorDTO();
        $collaboratorDTO->name = $collaboratorFields['name'];
        $collaboratorDTO->phone = $collaboratorFields['phone'];
        $collaboratorDTO->email = $collaboratorFields['email'];

        $this->collaboratorUseCase->saveCollaborator($collaboratorDTO);

        return redirect()->route('collaborators');
    }

    public function find($id): View
    {
        return view('welcome');
    }
}
