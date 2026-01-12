<?php

namespace App\Repository;

use App\Models\Collaborator;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorRepository
{
    public function getAllCollaborators(): Collection
    {
        return Collaborator::with('sales')->get();
   }

   public function saveCollaborator(Collaborator $collaborator){
        $collaborator->save();
   }
}
