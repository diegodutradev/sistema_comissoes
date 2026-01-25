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
                @php
                    $months = [
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                    ];
                @endphp

                <form method="get" class="row g-3 align-items-end">

                    {{-- COLABORADOR --}}
                    <div class="col-md-4">
                        <label class="form-label small-muted">Colaborador</label>
                        <select name="collaborator_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($collaborators as $c)
                                <option value="{{ $c->id }}"
                                    @selected(request('collaborator_id') == $c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- MÊS --}}
                    <div class="col-md-3">
                        <label class="form-label small-muted">Mês</label>
                        <select name="month" class="form-select" required>
                            @foreach($months as $number => $label)
                                <option value="{{ $number }}"
                                    @selected(request('month', now()->month) == $number)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ANO --}}
                    <div class="col-md-3">
                        <label class="form-label small-muted">Ano</label>
                        <input type="number"
                               name="year"
                               class="form-control"
                               min="2020"
                               value="{{ request('year', now()->year) }}">
                    </div>

                    {{-- BOTÃO --}}
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

    {{-- GRÁFICO MENOR --}}
    @isset($summary)
        <div class="col-12">
            <div class="card card-highlight">
                <div class="card-body">
                    <h6 class="mb-3">Distribuição de Pagamentos</h6>

                    <div class="d-flex justify-content-center">
                        <div style="max-width: 300px;">
                            <canvas id="paymentsChart"></canvas>
                        </div>
                    </div>

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
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endif
@endpush