<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Comissões Saúde — Controle</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Pequenos ajustes visuais */
        body { background: #f7f9fb; }
        .brand { font-weight: 700; letter-spacing: 0.2px; }
        .card-highlight { box-shadow: 0 4px 14px rgba(20,20,40,0.04); }
        .small-muted { font-size: .85rem; color:#6c757d; }
        .table .actions { min-width: 260px; }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand brand" href="">
            Comissões Saúde
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">
                        Colaboradores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">
                        Nova Venda
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    {{-- Flash messages --}}
    @if(session()->has('success') || session()->has('error') || session()->has('warning'))
        <div class="mb-3">
            @foreach (['success', 'error', 'warning'] as $type)
                @if(session()->has($type))
                    <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }}
                                alert-dismissible fade show"
                         role="alert">
                        {{ session($type) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @yield('content')
</div>

<footer class="small-muted text-center py-3">
    <div class="container">
        <span>&copy; {{ now()->year }} Diego Dutra</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
