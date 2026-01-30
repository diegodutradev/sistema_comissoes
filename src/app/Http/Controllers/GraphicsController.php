<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class GraphicsController extends Controller
{
    public function index(Request $request): View
    {
        $collaborators = Collaborator::orderBy('name')->get();

        $summary = null;

        if ($request->filled(['collaborator_id', 'month', 'year'])) {

            $start = Carbon::create(
                $request->year,
                $request->month,
                1
            )->startOfMonth();

            $end = (clone $start)->endOfMonth();

            $installments = Installment::whereHas('sale', function ($q) use ($request) {
                    $q->where('collaborator_id', $request->collaborator_id);
                })
                ->whereBetween('collaborator_receipt_date', [$start, $end])
                ->get();

            $received = $installments
                ->where('collaborator_paid', true)
                ->sum('amount');

            $pending = $installments
                ->where('collaborator_paid', false)
                ->sum('amount');

            $summary = [
                'received' => $received,
                'pending'  => $pending,
                'total'    => $received + $pending,
            ];
        }

        return view('graphics', compact(
            'collaborators',
            'summary'
        ));
    }
}