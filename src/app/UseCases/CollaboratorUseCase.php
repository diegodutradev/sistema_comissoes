<?php

namespace App\UseCases;

use App\DTO\CollaboratorDTO;
use App\Models\Collaborator;
use App\Repository\Contracts\CollaboratorRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorUseCase
{
    protected CollaboratorRepositoryInterface $collaboratorRepository;

    public function __construct(CollaboratorRepositoryInterface $collaboratorRepository)
    {
        $this->collaboratorRepository = $collaboratorRepository;
    }
    public function getAllCollaborators(): Collection
    {
        return $this->collaboratorRepository->getAllCollaborators();
    }

    public function saveCollaborator(CollaboratorDTO $collaboratorDTO): bool {
        $collaborator = new Collaborator();
        $collaborator->name = $collaboratorDTO->name;
        $collaborator->phone = $collaboratorDTO->phone;
        $collaborator->email = $collaboratorDTO->email;
        if (empty($collaborator->name)) {
           return false;
        }
        return $this->collaboratorRepository->saveCollaborator($collaborator);
    }
}
