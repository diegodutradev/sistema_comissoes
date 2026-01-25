@extends('base')

@section('content')
<div class="row g-4">

    {{-- HEADER --}}
    <div class="col-12">
        <h2 class="h4 mb-0">Relatórios de Comissões</h2>
        <div class="small-muted">
            Acompanhe valores pagos e pendentes por colaborador
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="col-12">
        <div class="card card-highlight">
            <div class="card-body">
                <form method="get" class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label small-muted">Colaborador</label>
                        <select name="collaborator_id" class="form-select">
                            <option value="">Selecione</option>
                            @foreach($collaborators as $c)
                                <option value="{{ $c->id }}"
                                    @selected(request('collaborator_id') == $c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small-muted">Mês</label>
                        <input type="number"
                               name="month"
                               class="form-control"
                               min="1" max="12"
                               value="{{ request('month') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small-muted">Ano</label>
                        <input type="number"
                               name="year"
                               class="form-control"
                               value="{{ request('year') }}">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            Filtrar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- CARDS DE RESUMO --}}
    @isset($summary)
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="small-muted">Já recebido</div>
                    <div class="fs-4 text-success">
                        R$ {{ number_format($summary['received'], 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="small-muted">A receber</div>
                    <div class="fs-4 text-warning">
                        R$ {{ number_format($summary['pending'], 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="small-muted">Total no período</div>
                    <div class="fs-4">
                        R$ {{ number_format($summary['total'], 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    @endisset

    {{-- GRÁFICO --}}
    @isset($summary)
        <div class="col-12">
            <div class="card card-highlight">
                <div class="card-body">
                    <h6 class="mb-3">Distribuição de Pagamentos</h6>
                    <canvas id="paymentsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    @endisset

</div>
@endsection

@push('scripts')
@if(isset($summary))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('paymentsChart');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Já recebido', 'A receber'],
            datasets: [{
                data: [
                    {{ $summary['received'] }},
                    {{ $summary['pending'] }}
                ]
            }]
        }
    });
</script>
@endif
@endpush