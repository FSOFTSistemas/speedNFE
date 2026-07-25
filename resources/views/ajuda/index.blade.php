@extends('adminlte::page')

@section('title', 'Central de Ajuda')

@section('content_header')
@stop

@section('content')
    <div class="help-hero">
        <h1>Como podemos ajudar?</h1>
        <p>Tutoriais, passo a passo e códigos de rejeição da SEFAZ</p>
        <form action="{{ route('ajuda.index') }}" method="GET" class="help-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" name="q" class="form-control" value="{{ $termo }}"
                placeholder="Pesquise um assunto, ex: certificado digital, chassi, rejeição 204...">
        </form>
    </div>

    @if (!is_null($resultados))
        <div class="card card-main" style="padding: 30px;">
            <h5 style="font-weight: 700;" class="mb-4">
                {{ count($resultados) }} {{ count($resultados) === 1 ? 'resultado' : 'resultados' }} para "{{ $termo }}"
            </h5>

            @forelse ($resultados as $artigo)
                @php $categoriaArtigo = collect($categorias)->firstWhere('slug', $artigo['categoria']); @endphp
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
                    <span class="badge badge-light">{{ $categoriaArtigo['titulo'] ?? '' }}</span>
                </a>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-search fa-2x mb-3"></i>
                    <p class="mb-0">Nenhum resultado encontrado para "{{ $termo }}". Tente outro termo de busca.</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="help-category-grid mb-4">
            @foreach ($categorias as $categoria)
                <a href="{{ route('ajuda.categoria', $categoria['slug']) }}" class="help-category-card">
                    <div class="stat-icon {{ $categoria['cor'] }}"><i class="{{ $categoria['icone'] }}"></i></div>
                    <h5>{{ $categoria['titulo'] }}</h5>
                    <p>{{ $categoria['descricao'] }}</p>
                </a>
            @endforeach
        </div>

        @if (count($populares))
            <div class="card card-main" style="padding: 30px;">
                <h5 style="font-weight: 700;" class="mb-3"><i class="fas fa-star text-warning mr-2"></i>Mais acessados</h5>
                @foreach ($populares as $artigo)
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
                @endforeach
            </div>
        @endif
    @endif
@stop
