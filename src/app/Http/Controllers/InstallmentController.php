<?php

namespace App\Http\Controllers;

use App\UseCases\InstallmentUseCase;
use App\Models\Installment;
use App\Repository\InstallmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class InstallmentController
{
    protected InstallmentUseCase $installmentUseCase;

    public function __construct()
    {
        $this->installmentUseCase = new InstallmentUseCase(new InstallmentRepository());
    }

    public function markClientPaid(Request $request, int $instId)
    {
        $inst = Installment::with('sale')->find($instId);

        if (!$inst) {
            return redirect()
                ->route('index')
                ->with('danger', 'Parcela não encontrada');
        }

        // Data informada ou hoje
        $dateStr = $request->input('client_paid_date');

        $clientPaidDate = $dateStr
            ? Carbon::createFromFormat('Y-m-d', $dateStr)
            : now();

        DB::transaction(function () use ($inst, $clientPaidDate) {

            /**
             * Marca cliente como pago
             */
            $inst->client_paid = true;
            $inst->client_paid_date = $clientPaidDate;
            $inst->collaborator_receipt_date =
                $this->computeCollaboratorReceiptDate($clientPaidDate);

            $inst->save();

            /**
             * Se for a 1ª parcela, recalcula e distribui o extra
             */
            if ($inst->index === 1) {
                $collabId = $inst->sale->collaborator_id;
                $month = $clientPaidDate->month;
                $year  = $clientPaidDate->year;

                // Todas as 1ªs parcelas pagas no mês
                $firstPaid = $this
                    ->getFirstPaidInstallmentsForCollaboratorMonth(
                        $collabId,
                        $month,
                        $year
                    );

                $totalPaidSales = $firstPaid->sum(function ($fi) {
                    return $fi->sale->amount;
                });

                $multiplier = $this->computeCommissionMultiplier($totalPaidSales);

                /**
                 * Atualiza parcelas 2 e 3 de cada venda
                 */
                foreach ($firstPaid as $fi) {
                    $sale = $fi->sale;

                    $extra = round(
                        $sale->amount * ($multiplier - 1.0),
                        2
                    );

                    $part = round($extra / 2, 2);

                    Installment::where('sale_id', $sale->id)
                        ->whereIn('index', [2, 3])
                        ->update(['amount' => $part]);
                }
            }
        });

        if ($inst->index === 1) {
            return redirect()
                ->back()
                ->with(
                    'success',
                    'Pagamento do cliente registrado; parcelas 2 e 3 atualizadas com base no total do mês.'
                );
        }

        return redirect()
            ->back()
            ->with('success', 'Pagamento do cliente registrado.');
    }

    public function markCollaboratorPaid(Request $request, int $inst)
    {
        $dateStr = $request->input('collaborator_paid_date');
        $installment = $this->installmentUseCase->markCollaboratorPaid($inst, $dateStr);
        if (!$installment) {
            return redirect()
                ->route('index')
                ->with('danger', 'Parcela não encontrada');
        }
        return redirect()
            ->back()
            ->with('success', 'Pagamento ao colaborador marcado.');
    }
    
    /**
     * Equivalente ao compute_collaborator_receipt_date do Python
     */
    private function computeCollaboratorReceiptDate(Carbon $clientPaidDate): Carbon
    {
        if ($clientPaidDate->day <= 5) {
            return Carbon::create(
                $clientPaidDate->year,
                $clientPaidDate->month,
                20
            );
        }

        return $clientPaidDate
            ->copy()
            ->addMonthNoOverflow()
            ->day(5);
    }


    private function getFirstPaidInstallmentsForCollaboratorMonth(
        int $collabId,
        int $month,
        int $year
    ): Collection {
        return Installment::query()
            ->join('sales', 'sales.id', '=', 'installments.sale_id')
            ->where('sales.collaborator_id', $collabId)
            ->where('installments.index', 1)
            ->where('installments.client_paid', true)
            ->whereNotNull('installments.client_paid_date')
            ->whereMonth('installments.client_paid_date', $month)
            ->whereYear('installments.client_paid_date', $year)
            ->with('sale') // importante, pois você usa $fi->sale depois
            ->select('installments.*')
            ->get();
    }

    private function computeCommissionMultiplier(float $amount): float
    {
        if ($amount <= 2000) {
            return 1.2;
        }

        if ($amount <= 4000) {
            return 1.4;
        }

        return 1.6;
    }
}
