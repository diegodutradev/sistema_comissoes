<?php

namespace App\Http\Controllers;

use App\DTO\CollaboratorDTO;
use Illuminate\Http\Request;
use App\Repository\CollaboratorRepository;
use App\UseCases\CollaboratorUseCase;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
        $collaboratorFields = $req->post();
        $collaboratorDTO = new CollaboratorDTO();
        $collaboratorDTO->name = is_null($collaboratorFields['name']) ? '' : $collaboratorFields['name'];
        $collaboratorDTO->phone = is_null($collaboratorFields['phone']) ? '' : $collaboratorFields['phone'];
        $collaboratorDTO->email = is_null($collaboratorFields['email']) ? '' : $collaboratorFields['email'];
        $this->collaboratorUseCase->saveCollaborator($collaboratorDTO);
        return redirect()->route('collaborators');
    }

    public function find(Request $request, int $cid)
    {
         // Filtro de mês e ano (default = atual)
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);
    
        $collaboratorDetails = $this->collaboratorUseCase->findOne($cid, $month, $year);
        if (!$collaboratorDetails) {
            return redirect()
                ->route('index')
                ->with('danger', 'Colaborador não encontrado');
        }
        return view('collaborator_detail', [
            'collaborator'             => $collaboratorDetails->collaborator,
            'total_vendido'            => $collaboratorDetails->totalVendido,
            'percentual'               => $collaboratorDetails->percentual,
            'valor_comissao'           => $collaboratorDetails->valorComissao,
            'total_from_current_sales' => $collaboratorDetails->totalFromCurrentSales,
            'total_from_previous_sales'=> $collaboratorDetails->totalFromPreviousSales,
            'total_to_pay'             => $collaboratorDetails->totalToPay,
            'todas_vendas'             => $collaboratorDetails->todasVendas,
            'month'                    => $collaboratorDetails->month,
            'year'                     => $collaboratorDetails->year,
        ]);
    }
}
