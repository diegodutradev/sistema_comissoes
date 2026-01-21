@extends('base')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-body">
      <h4 class="mb-3">Nova Venda</h4>

      {{-- <form method="post" action="{{ route('sale.new') }}"> --}}
      <form method="post" action="">
        @csrf

        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Colaborador</label>
            <select name="collaborator_id" class="form-select" required>
              <option value="">Escolha...</option>
              @foreach($collaborators as $c)
                <option value="{{ $c->id }}">
                  {{ $c->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Nome do cliente</label>
            <input
              name="client_name"
              class="form-control"
              required
              value="{{ old('client_name') }}"
            >
          </div>

          <div class="col-md-4">
            <label class="form-label">Valor da venda (R$)</label>
            <input
              type="number"
              step="0.01"
              name="amount"
              class="form-control"
              required
              value="{{ old('amount') }}"
            >
          </div>

          <div class="col-md-4">
            <label class="form-label">1º pagamento do cliente</label>
            <input
              type="date"
              name="client_first_payment_date"
              class="form-control"
              required
              value="{{ old('client_first_payment_date') }}"
            >
          </div>

          <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">
              Salvar venda
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>
@endsection
