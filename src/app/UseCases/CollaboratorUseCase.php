<?php

namespace App\UseCases;

use App\Models\Collaborator;
use App\Repository\CollaboratorRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorUseCase
{
    protected CollaboratorRepository $collaboratorRepository;

    public function __construct(CollaboratorRepository $collaboratorRepository)
    {
        $this->collaboratorRepository = $collaboratorRepository;
    }
    public function getAllCollaborators(): Collection
    {
        return $this->collaboratorRepository->getAllCollaborators();
    }

    public function saveCollaborator($collaboratorDTO){
        $collaborator = new Collaborator();
        $collaborator->name = $collaboratorDTO->name;
        $collaborator->phone = $collaboratorDTO->phone;
        $collaborator->email = $collaboratorDTO->email;

        if(is_null($collaborator->name) || empty($collaborator->name)){
            throw new Exception("Nome é Obrigatório!");
        } 

        $this->collaboratorRepository->saveCollaborator($collaborator);
    }
}
