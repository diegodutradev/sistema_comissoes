@extends('base')

@section('content')
<style>
    .stat-card {
        border: 1px solid #f0f0f0;
        transition: all .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,.06);
    }

    .installment-card {
        background: #fafafa;
        transition: all .15s ease;
    }

    .installment-card:hover {
        background: #fff;
        box-shadow: 0 4px 14px rgba(0,0,0,.05);
    }

    .action-btn {
        min-width: 150px;
        height: 34px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        line-height: 1;
        white-space: nowrap;
    }

    .btn-status {
        pointer-events: none;
        opacity: .85;
    }
</style>

<div class="row g-4">

    {{-- HEADER --}}
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h2 class="h4 mb-0">Colaborador — {{ $collaborator->name }}</h2>
                <div class="small text-muted">
                    {{ $collaborator->phone ?? '-' }} • {{ $collaborator->email ?? '-' }}
                </div>
            </div>
            <a href="{{ route('sale_new') }}" class="btn btn-outline-primary btn-sm">
                Registrar venda
            </a>
        </div>
    </div>

    {{-- LEFT COLUMN — RESUMO --}}
    <div class="col-lg-4">
        <div class="row g-3">

            <div class="col-6">
                <div class="card stat-card text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Vendas</div>
                        <div class="fs-5 fw-semibold text-primary">
                            {{ $stats['sales'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card stat-card text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Parcelas</div>
                        <div class="fs-5 fw-semibold">
                            {{ $stats['installments'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card stat-card text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Recebidas</div>
                        <div class="fs-5 fw-semibold text-success">
                            {{ $stats['paid_installments'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card stat-card text-center">
                    <div class="card-body py-3">
                        <div class="small text-muted">Pendentes</div>
                        <div class="fs-5 fw-semibold text-warning">
                            {{ $stats['pending_installments'] }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- VALORES --}}
            <div class="col-12">
                <div class="card stat-card">
                    <div class="card-body py-3 small text-muted">

                        <div class="d-flex justify-content-between">
                            <span>Recebido</span>
                            <strong class="text-success">
                                R$ {{ number_format($stats['received'], 2, ',', '.') }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mt-1">
                            <span>A receber</span>
                            <strong class="text-warning">
                                R$ {{ number_format($stats['pending'], 2, ',', '.') }}
                            </strong>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between">
                            <span>Total</span>
                            <strong>
                                R$ {{ number_format($stats['total'], 2, ',', '.') }}
                            </strong>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- RIGHT COLUMN — VENDAS --}}
    <div class="col-lg-8">
        <div class="row g-3">

            @forelse($todas_vendas as $s)
                <div class="col-12">
                    <div class="card shadow-sm">

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $s->client_name }}</strong>
                                <div class="small text-muted">
                                    Venda #{{ $s->id }} •
                                    R$ {{ number_format($s->amount, 2, ',', '.') }}
                                </div>
                            </div>
                            <span class="badge bg-secondary">
                                {{ $s->installments->count() }} parcelas
                            </span>
                        </div>

                        <div class="card-body">
                            @foreach($s->installments as $inst)
                                <div class="installment-card border rounded p-2 mb-2">

                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Parcela #{{ $inst->index }}</strong>
                                        <span class="fw-semibold">
                                            R$ {{ number_format($inst->amount, 2, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="small text-muted">
                                        Vencimento:
                                        {{ optional($inst->client_due_date)->format('d/m/Y') }}
                                    </div>

                                    <div class="mt-2 d-flex flex-wrap gap-2">

                                        {{-- CLIENTE --}}
                                        @if($inst->client_paid)
                                            <button class="btn btn-sm btn-success action-btn btn-status">
                                                Cliente pagou
                                            </button>
                                        @else
                                            <form method="post" action="{{ route('markClientPaid', $inst->id) }}">
                                                @csrf
                                                <input type="hidden" name="client_paid_date" value="{{ now()->toDateString() }}">
                                                <button class="btn btn-sm btn-outline-success action-btn">
                                                    Cliente pagou
                                                </button>
                                            </form>
                                        @endif

                                        {{-- COLABORADOR --}}
                                        @if($inst->collaborator_paid)
                                            <button class="btn btn-sm btn-primary action-btn btn-status">
                                                Repasse feito
                                            </button>
                                        @elseif($inst->client_paid)
                                            <form method="post" action="{{ route('markCollaboratorPaid', $inst->id) }}">
                                                @csrf
                                                <input type="hidden" name="collaborator_paid_date" value="{{ now()->toDateString() }}">
                                                <button class="btn btn-sm btn-outline-primary action-btn">
                                                    Colab. recebeu
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary action-btn btn-status">
                                                Aguardando cliente
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card card-body text-center small text-muted">
                        Nenhuma venda registrada.
                    </div>
                </div>
            @endforelse

        </div>
    </div>

</div>
@endsection