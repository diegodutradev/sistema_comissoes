<?php

namespace App\Http\Controllers;

use App\DTO\CollaboratorDTO;
use Illuminate\Http\Request;
use App\Repository\CollaboratorRepository;
use App\UseCases\CollaboratorUseCase;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Collaborator;
use App\Models\Installment;
use App\Models\Sale;

class CollaboratorController extends Controller
{
    protected CollaboratorUseCase $collaboratorUseCase;

    public function __construct()
    {
        $this->collaboratorUseCase = new CollaboratorUseCase(new CollaboratorRepository());
    }

    public function index(): View
    {
        $collaborators = $this->collaboratorUseCase->getAllCollaborators();
        return view('collaborators', ['collaborators' => $collaborators]);
    }

    public function create(Request $req): RedirectResponse
    {
        // dd($req->post());
        $collaboratorFields = $req->post();

        $collaboratorDTO = new CollaboratorDTO();
        $collaboratorDTO->name = $collaboratorFields['name'];
        $collaboratorDTO->phone = $collaboratorFields['phone'];
        $collaboratorDTO->email = $collaboratorFields['email'];

        $this->collaboratorUseCase->saveCollaborator($collaboratorDTO);

        return redirect()->route('collaborators');
    }

    public function find(Request $request, int $cid)
    {
        $collab = Collaborator::find($cid);

        if (!$collab) {
            return redirect()
                ->route('index')
                ->with('danger', 'Colaborador não encontrado');
        }

        // Filtro de mês e ano (default = atual)
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        /**
         * --- Total de vendas cujo 1º pagamento foi pago neste mês ---
         */
        $firstPaidInstallments =
            $this->getFirstPaidInstallmentsForCollaboratorMonth(
                $cid,
                $month,
                $year
            );

        // Soma do valor original das vendas (sale.amount)
        $totalVendido = $firstPaidInstallments->sum(function ($inst) {
            return $inst->sale->amount;
        });

        // Percentual e valor de comissão
        $percentual    = $this->computeCommissionMultiplier($totalVendido);
        $valorComissao = round($totalVendido * ($percentual - 1.0), 2);

        /**
         * --- Parcelas que CAEM neste mês para o colaborador ---
         */
        $parcelasAPagar = Installment::query()
            ->join('sales', 'sales.id', '=', 'installments.sale_id')
            ->where('sales.collaborator_id', $cid)
            ->where('installments.client_paid', true)
            ->whereNotNull('installments.collaborator_receipt_date')
            ->whereMonth('installments.collaborator_receipt_date', $month)
            ->whereYear('installments.collaborator_receipt_date', $year)
            ->select('installments.*')
            ->with('sale')
            ->get();

        /**
         * --- Separar origem das parcelas ---
         * vendas do mesmo mês vs vendas anteriores
         */
        $totalFromCurrentSales  = 0.0;
        $totalFromPreviousSales = 0.0;

        foreach ($parcelasAPagar as $p) {
            $saleMonth = $p->sale->client_first_payment_date->month;
            $saleYear  = $p->sale->client_first_payment_date->year;

            if ($saleMonth == $month && $saleYear == $year) {
                $totalFromCurrentSales += $p->amount;
            } else {
                $totalFromPreviousSales += $p->amount;
            }
        }

        $totalToPay = round(
            $totalFromCurrentSales + $totalFromPreviousSales,
            2
        );

        /**
         * --- Todas as vendas para listagem ---
         */
        $todasVendas = Sale::where('collaborator_id', $cid)->get();

        return view('collaborator_detail', [
            'collaborator'             => $collab,
            'total_vendido'            => $totalVendido,
            'percentual'               => $percentual,
            'valor_comissao'           => $valorComissao,
            'total_from_current_sales' => $totalFromCurrentSales,
            'total_from_previous_sales'=> $totalFromPreviousSales,
            'total_to_pay'             => $totalToPay,
            'todas_vendas'             => $todasVendas,
            'month'                    => $month,
            'year'                     => $year,
        ]);
    }

    /**
     * Retorna parcelas com index = 1, pagas pelo cliente,
     * cujo pagamento ocorreu no mês/ano informado
     */
    private function getFirstPaidInstallmentsForCollaboratorMonth(
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

    /**
     * Mesma regra de comissão do Python
     */
    private function computeCommissionMultiplier(float $amount): float
    {
        if ($amount <= 2000) {
            return 1.2;
        } elseif ($amount <= 4000) {
            return 1.4;
        }
        return 1.6;
    }
}
