<?php

namespace App\Repository;

use App\Models\Sale;

class SaleRepository {
    public function getSaleByCollaboratorID(int $collabId) {
        return Sale::where('collaborator_id', $collabId)->get();
    }
}