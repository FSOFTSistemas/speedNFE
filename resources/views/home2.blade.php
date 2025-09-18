@extends('adminlte::page')

@section('title', 'Dashboard')

{{-- Adiciona os estilos customizados para a página --}}
@push('css')
<style>
    /* Importa a fonte Poppins para consistência com a tela de login */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    /* Variáveis de cor para fácil manutenção */
    :root {
        --primary-color: #0a2540;
        --accent-blue: #3498db;
        --accent-purple: #8e44ad;
        --accent-green: #2ecc71;
        --accent-yellow: #f1c40f;
        --accent-red: #e74c3c;
        --card-bg: #ffffff;
        --text-light: #f8f9fa;
        --text-dark: #343a40;
        --shadow-color: rgba(0, 0, 0, 0.08);
    }

    /* Estilo base da página */
    body {
        font-family: 'Poppins', sans-serif;
    }

    /* Estilo do novo card de estatísticas */
    .stat-card {
        background: var(--card-bg);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
        padding: 25px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    /* Conteúdo interno do card */
    .stat-card .inner {
        position: relative;
        z-index: 2;
    }

    .stat-card h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-dark);
    }

    .stat-card p {
        font-size: 1rem;
        color: #6c757d;
    }

    /* Ícone decorativo no fundo */
    .stat-card .icon {
        position: absolute;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        font-size: 80px;
        color: rgba(0, 0, 0, 0.07);
        z-index: 1;
        transition: transform 0.4s ease, color 0.4s ease;
    }

    .stat-card:hover .icon {
        transform: translateY(-50%) scale(1.1);
    }

    /* Rodapé do card com o link */
    .stat-card-footer {
        display: block;
        padding: 10px 0 0 0;
        margin-top: 15px;
        border-top: 1px solid #eee;
        text-align: center;
        color: #6c757d;
        text-decoration: none;
        font-weight: 500;
        z-index: 2;
        position: relative;
        transition: color 0.3s ease;
    }

    /* Variações de cor para cada card */
    .stat-card.blue { border-left: 5px solid var(--accent-blue); }
    .stat-card.purple { border-left: 5px solid var(--accent-purple); }
    .stat-card.green { border-left: 5px solid var(--accent-green); }
    .stat-card.yellow { border-left: 5px solid var(--accent-yellow); }
    .stat-card.red { border-left: 5px solid var(--accent-red); }

    .stat-card.blue .stat-card-footer:hover { color: var(--accent-blue); }
    .stat-card.purple .stat-card-footer:hover { color: var(--accent-purple); }
    .stat-card.green .stat-card-footer:hover { color: var(--accent-green); }
    .stat-card.yellow .stat-card-footer:hover { color: var(--accent-yellow); }
    .stat-card.red .stat-card-footer:hover { color: var(--accent-red); }
    
    /* Container dos gráficos */
    .chart-container {
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px var(--shadow-color);
    }

</style>
@endpush

{{-- Adiciona a biblioteca Chart.js --}}
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush


@section('content_header')
    <div class="row">
        <div class="col">
        </div>
    </div>
@stop

@section('content')
    <h1>Bem vindo {{Auth::user()->name}}</h1>
@stop
