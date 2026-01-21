<?php

namespace App\UseCases;

use App\DTO\CollaboratorDTO;
use App\Models\Collaborator;
use App\Models\Sale;
use App\Models\Installment;
use App\Models\CollaboratorDetails;
use App\Repository\Contracts\CollaboratorRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorUseCase
{
    protected CollaboratorRepositoryInterface $collaboratorRepository;

    public function __construct(CollaboratorRepositoryInterface $collaboratorRepository)
    {
        $this->collaboratorRepository = $collaboratorRepository;
    }
    public function getAllCollaborators(): Collection
    {
        return $this->collaboratorRepository->getAllCollaborators();
    }

    public function saveCollaborator(CollaboratorDTO $collaboratorDTO): bool {
        $collaborator = new Collaborator();
        $collaborator->name = $collaboratorDTO->name;
        $collaborator->phone = $collaboratorDTO->phone;
        $collaborator->email = $collaboratorDTO->email;
        if (empty($collaborator->name)) {
           return false;
        }
        return $this->collaboratorRepository->saveCollaborator($collaborator);
    }

    public function findOne(int $cid, int $month, int $year): ?CollaboratorDetails {
        $collab = Collaborator::find($cid);

        if (!$collab) {
           return null;
        }

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
        
        $collaboratorDetails = new CollaboratorDetails();
        $collaboratorDetails->collaborator = $collab;
        $collaboratorDetails->totalVendido = $totalVendido;
        $collaboratorDetails->percentual = $percentual;
        $collaboratorDetails->valorComissao = $valorComissao;
        $collaboratorDetails->totalFromCurrentSales = $totalFromCurrentSales;
        $collaboratorDetails->totalFromPreviousSales = $totalFromPreviousSales;
        $collaboratorDetails->totalToPay = $totalToPay;
        $collaboratorDetails->todasVendas = $todasVendas;
        $collaboratorDetails->month = $month;
        $collaboratorDetails->year = $year;

        return $collaboratorDetails;
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
