@extends('base')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">

                <h4 class="mb-4 text-center">Nova Venda</h4>

                <form method="post" action="">
                    @csrf

                    {{-- Colaborador --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Colaborador</label>
                        <select
                            name="collaborator_id"
                            id="collaborator_id"
                            class="form-select @error('collaborator_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecione um colaborador</option>
                            @foreach($collaborators as $c)
                                <option
                                    value="{{ $c->id }}"
                                    {{ old('collaborator_id') == $c->id ? 'selected' : '' }}
                                >
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('collaborator_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CAMPOS BLOQUEADOS ATÉ ESCOLHER COLABORADOR --}}
                    <fieldset id="sale-fields" disabled>

                        {{-- Nome do cliente --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome do cliente</label>
                            <input
                                type="text"
                                name="client_name"
                                class="form-control @error('client_name') is-invalid @enderror"
                                placeholder="Ex: João da Silva"
                                value="{{ old('client_name') }}"
                                required
                            >
                            @error('client_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo de Cliente</label>
                            <select
                                name="client_type"
                                id="client_type"
                                class="form-select @error('client_type') is-invalid @enderror"
                                required>
                                <option value="">Selecione um tipo de cliente</option>
                                <option value="1">PME</option>
                                <option value="2">Pessoa Física</option>
                            </select>
                        </div>

                        <div class="mb-3" id="bonification_amount_div" hidden>
                            <label class="form-label fw-semibold">Valor da bonificação</label>
                            <input
                                type="text"
                                name="bonification_amount"
                                id="bonification_amount"
                                class="form-control @error('bonification_amount') is-invalid @enderror"
                                placeholder="Ex: 1000"
                                value="{{ old('bonification_amount') }}"
                            >
                        </div>

                        {{-- Valor da venda --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Valor da venda</label>

                            <input
                                type="text"
                                id="amount_display"
                                class="form-control"
                                placeholder="R$ 0,00"
                                autocomplete="off"
                                required
                            >

                            <input
                                type="hidden"
                                name="amount"
                                id="amount"
                                value="{{ old('amount') }}"
                            >

                            @error('amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Data do primeiro pagamento --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Data do 1º pagamento do cliente
                            </label>
                            <input
                                type="date"
                                name="client_first_payment_date"
                                class="form-control @error('client_first_payment_date') is-invalid @enderror"
                                value="{{ old('client_first_payment_date') }}"
                                required
                            >
                            @error('client_first_payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botão --}}
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg">
                                Salvar venda
                            </button>
                        </div>

                    </fieldset>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- JS: Ativar campos + Formatação BRL --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const collaborator = document.getElementById('collaborator_id');
    const saleFields  = document.getElementById('sale-fields');

    const display = document.getElementById('amount_display');
    const hidden  = document.getElementById('amount');

    // Ativa/desativa campos conforme colaborador
    function toggleFields() {
        saleFields.disabled = !collaborator.value;
    }

    collaborator.addEventListener('change', toggleFields);

    // Aplica estado inicial (caso tenha old())
    toggleFields();

    // ====== FORMATAÇÃO MOEDA BRL ======
    function formatBRL(value) {
        value = value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        value = value.replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'R$ ' + value;
    }

    display.addEventListener('input', function () {
        const raw = this.value.replace(/\D/g, '');
        hidden.value = raw ? (raw / 100).toFixed(2) : '';
        this.value = raw ? formatBRL(raw) : '';
    });

    // Reaplica valor antigo (old)
    if (hidden.value) {
        display.value = formatBRL(hidden.value.replace('.', ''));
    }
    const clientType = document.getElementById('client_type');
    clientType.addEventListener('change', function() {
        if (this.value == 1) {
            let bonificationAmountDiv = document.getElementById('bonification_amount_div')
            bonificationAmountDiv.setAttribute('hidden', 'hidden')
            let bonificationAmountInput = document.getElementById('bonification_amount')
            bonificationAmountInput.removeAttribute('required')

        } else if (this.value == 2) {
            let bonificationAmountDiv = document.getElementById('bonification_amount_div')
            bonificationAmountDiv.removeAttribute('hidden')
            let bonificationAmountInput = document.getElementById('bonification_amount')
            bonificationAmountInput.setAttribute('required', 'required')

        }

    })
});
</script>
@endsection
