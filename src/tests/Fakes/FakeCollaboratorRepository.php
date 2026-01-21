<?php

namespace Tests\Fakes;

use App\Models\Collaborator;
use App\Repository\Contracts\CollaboratorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FakeCollaboratorRepository implements CollaboratorRepositoryInterface
{
    public function getAllCollaborators(): Collection
    {
        $collaborators =  new Collection();
        $collaborator = new Collaborator();
        $collaborator->name = 'Fulano';
        $collaborator->email = 'fulano@email.com';
        $collaborator->phone = '9912345678';
        $collaborators->add($collaborator);
        return $collaborators;
    }

    public function saveCollaborator(Collaborator $collaborator): bool
    {
        return true;
    }
}
