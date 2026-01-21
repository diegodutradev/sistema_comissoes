<?php

namespace App\UseCases;

use App\Models\Installment;
use App\Repository\InstallmentRepository;
use Carbon\Carbon;

class InstallmentUseCase
{
    public InstallmentRepository $installmentRepository;

    public function __construct(InstallmentRepository $installmentRepository)
    {
        $this->installmentRepository = $installmentRepository;
    }
    
    public function markCollaboratorPaid(int $inst, $dateStr) {
        $installment = Installment::find($inst);
        if (!$installment) {    
           return null;
        }

        $paidDate = $dateStr
            ? Carbon::createFromFormat('Y-m-d', $dateStr)->toDateString()
            : Carbon::today()->toDateString();

        $installment->collaborator_paid = true;
        $installment->collaborator_paid_date = $paidDate;
        $installment->save();
        return true;
    }
}
