<?php

namespace App\Repository;

use App\Models\Collaborator;
use App\Repository\Contracts\CollaboratorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorRepository implements CollaboratorRepositoryInterface
{
    public function getAllCollaborators(): Collection
    {
        return Collaborator::with('sales')->get();
    }

    public function saveCollaborator(Collaborator $collaborator): bool
    {
        return $collaborator->save();
    }
    public function findOne(int $cid)
    {
        return Collaborator::find($cid);
    }
}
