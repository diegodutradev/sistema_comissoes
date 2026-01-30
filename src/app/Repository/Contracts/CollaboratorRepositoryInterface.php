<?php

namespace App\Repository\Contracts;

use App\Models\Collaborator;
use Illuminate\Database\Eloquent\Collection;

interface CollaboratorRepositoryInterface
{
    public function getAllCollaborators(): Collection;
    public function saveCollaborator(Collaborator $collaborator): bool;
    public function findOne(int $cid);
}
