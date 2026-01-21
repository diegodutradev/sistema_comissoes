<?php

namespace App\Repository;

use App\Models\Installment;

class InstallmentRepository {

    public function getFirstPaidInstallmentsForCollaboratorMonth(
        int $collabId,
        int $month,
        int $year
    ) {
        return Installment::query()
            ->join('sales', 'sales.id', '=', 'installments.sale_id')
            ->where('sales.collaborator_id', $collabId)
            ->where('installments.index', 1)
            ->where('installments.client_paid', true)
            ->whereNotNull('installments.client_paid_date')
            ->whereMonth('installments.client_paid_date', $month)
            ->whereYear('installments.client_paid_date', $year)
            ->select('installments.*')
            ->with('sale')
            ->get();
    }

    public function getInstallmentsToPay(  
        int $collabId,
        int $month,
        int $year
    ) {
        return Installment::query()
            ->join('sales', 'sales.id', '=', 'installments.sale_id')
            ->where('sales.collaborator_id', $collabId)
            ->where('installments.client_paid', true)
            ->whereNotNull('installments.collaborator_receipt_date')
            ->whereMonth('installments.collaborator_receipt_date', $month)
            ->whereYear('installments.collaborator_receipt_date', $year)
            ->select('installments.*')
            ->with('sale')
            ->get();
    }
}

