@extends('adminlte::page')

@section('title', $categoria['titulo'])

@section('content_header')
    <div class="row align-items-center">
        <div class="col-12">
            <div class="page-eyebrow">Central de Ajuda</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">{{ $categoria['titulo'] }}</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="help-breadcrumb">
        <a href="{{ route('ajuda.index') }}"><i class="fas fa-home mr-1"></i>Central de Ajuda</a>
        <span class="divider">/</span>
        <span class="text-dark">{{ $categoria['titulo'] }}</span>
    </div>

    <div class="card card-main" style="padding: 30px;">
        <div class="d-flex align-items-center mb-4">
            <div class="stat-icon {{ $categoria['cor'] }} mr-3"><i class="{{ $categoria['icone'] }}"></i></div>
            <div>
                <h5 class="mb-0" style="font-weight: 700;">{{ $categoria['titulo'] }}</h5>
                <div class="text-muted" style="font-size: 0.85rem;">{{ $categoria['descricao'] }}</div>
            </div>
        </div>

        @forelse ($artigos as $artigo)
            <a href="{{ route('ajuda.artigo', $artigo['slug']) }}" class="help-article-list-item">
                <div>
                    <div class="help-article-title">
                        @if (!empty($artigo['codigo']))
                            <span class="badge badge-codigo mr-1">{{ $artigo['codigo'] }}</span>
                        @endif
                        {{ $artigo['titulo'] }}
                    </div>
                    <div class="help-article-resumo">{{ $artigo['resumo'] }}</div>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
        @empty
            <p class="text-muted mb-0">Ainda não há artigos nesta categoria.</p>
        @endforelse
    </div>
@stop
