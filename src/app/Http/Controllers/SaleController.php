<?php

namespace App\Http\Controllers;

use App\UseCases\SaleUseCase;
use Illuminate\View\View;
use App\Models\Sale;
use App\Models\Collaborator;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController
{
    const CLIENTPME = 1;
    const CLIENTPF = 2;
    protected SaleUseCase $saleUseCase;

    public function __construct()
    {
        $this->saleUseCase = new SaleUseCase();
    }

    public function new(Request $request)
    {
        /**
         * =====================
         * GET
         * =====================
         */
        if ($request->isMethod('get')) {
            $collaborators = Collaborator::orderBy('name')->get();

            return view('sale_form', compact('collaborators'));
        }

        /**
         * =====================
         * POST
         * =====================
         */
        $collabId  = $request->input('collaborator_id');
        $clientName = $request->input('client_name');
        $amount = (float) ($request->input('amount') ?? 0);
        $firstPaymentStr = $request->input('client_first_payment_date');
        $clientType = $request->input('client_type');
        $bonificationAmount = $request->input('bonification_amount');

        if (!$collabId || !$clientName || !$amount || !$firstPaymentStr || !$clientType) {
            return redirect()
                ->route('sale_new')
                ->with('danger', 'Preencha todos os campos');
        }
        if ($clientType == self::CLIENTPF && !$bonificationAmount) {
            return redirect()
                ->route('sale_new')
                ->with('danger', 'Preencha todos os campos');
        }

        $clientFirstPaymentDate = Carbon::createFromFormat(
            'Y-m-d',
            $firstPaymentStr
        )->startOfDay();

        DB::transaction(function () use (
            $collabId,
            $clientName,
            $amount,
            $clientFirstPaymentDate,
            $clientType,
            $bonificationAmount
        ) {
            /**
             * Cria a venda
             */
            $sale = Sale::create([
                'collaborator_id' => (int) $collabId,
                'client_name' => $clientName,
                'amount' => $amount,
                'client_type' => $clientType,
                'client_first_payment_date' => $clientFirstPaymentDate,
            ]);

            if ($clientType == self::CLIENTPME) {
                /**
                 * Cria 3 parcelas
                 * 1ª: valor total
                 * 2ª e 3ª: 0.0
                 */
                for ($i = 0; $i < 3; $i++) {
                    Installment::create([
                        'sale_id' => $sale->id,
                        'index' => $i + 1,
                        'client_due_date' => $this->addMonths(
                            $clientFirstPaymentDate,
                            $i
                        ),
                        'amount' => $i === 0 ? round($amount, 2) : 0.0,
                    ]);
                }
            } elseif ($clientType == self::CLIENTPF) {
                for ($i = 0; $i < 2; $i++) {
                    Installment::create([
                        'sale_id' => $sale->id,
                        'index' => $i + 1,
                        'client_due_date' => $this->addMonths(
                            $clientFirstPaymentDate,
                            $i
                        ),
                        'amount' => $i === 0 ? round($amount, 2) : $bonificationAmount,
                    ]);
                }
            }
        });

        return redirect()
            ->route('collaborator_detail', $collabId)
            ->with('success', 'Venda cadastrada e parcelas geradas com sucesso.');
    }

    /**
     * Equivalente ao add_months do Python
     * (resolve meses com dias inválidos, ex: 31/02)
     */
    private function addMonths(Carbon $date, int $months): Carbon
    {
        return $date->copy()->addMonthsNoOverflow($months);
    }
}
