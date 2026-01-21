<?php

namespace App\UseCases;

use App\DTO\CollaboratorDTO;
use App\Models\Collaborator;
use App\Models\Sale;
use App\Models\CollaboratorDetails;
use App\Repository\Contracts\CollaboratorRepositoryInterface;
use App\Repository\InstallmentRepository;
use App\Repository\SaleRepository;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorUseCase
{
    protected CollaboratorRepositoryInterface $collaboratorRepository;
    protected InstallmentRepository $installmentRepository;
    protected SaleRepository $saleRepository;

    public function __construct(
        CollaboratorRepositoryInterface $collaboratorRepository)
    {
        $this->collaboratorRepository = $collaboratorRepository;
        $this->installmentRepository = new InstallmentRepository();
        $this->saleRepository = new SaleRepository();
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
        $collab = $this->collaboratorRepository->findOne($cid);
        if (!$collab) {
           return null;
        }

        $firstPaidInstallments =
            $this->installmentRepository->getFirstPaidInstallmentsForCollaboratorMonth(
                $cid,
                $month,
                $year
            );

        $totalVendido = $firstPaidInstallments->sum(function ($inst) {
            return $inst->sale->amount;
        });

        $percentual = $this->computeCommissionMultiplier($totalVendido);
        $valorComissao = round($totalVendido * ($percentual - 1.0), 2);

        $parcelasAPagar = $this->installmentRepository->getInstallmentsToPay($cid, $month, $year);

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

        $todasVendas = $this->saleRepository->getSaleByCollaboratorID($cid);  

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
