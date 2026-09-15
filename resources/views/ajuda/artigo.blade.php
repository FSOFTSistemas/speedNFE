@extends('adminlte::page')

@section('title', $artigo['titulo'])

@section('content_header')
    <div class="row align-items-center">
        <div class="col-12">
            <div class="page-eyebrow">Central de Ajuda</div>
            <h1 class="m-0 text-dark" style="font-weight: 700;">{{ $artigo['titulo'] }}</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="help-breadcrumb">
        <a href="{{ route('ajuda.index') }}"><i class="fas fa-home mr-1"></i>Central de Ajuda</a>
        @if ($categoria)
            <span class="divider">/</span>
            <a href="{{ route('ajuda.categoria', $categoria['slug']) }}">{{ $categoria['titulo'] }}</a>
        @endif
        <span class="divider">/</span>
        <span class="text-dark">{{ $artigo['titulo'] }}</span>
    </div>

    <div class="row">
        <div class="col-lg-9 mb-4">
            <div class="card card-main" style="padding: 32px;">
                @if (!empty($artigo['codigo']))
                    <span class="badge badge-codigo mb-3">Código {{ $artigo['codigo'] }}</span>
                @endif

                <div class="help-article-body">
                    @foreach ($artigo['blocos'] as $bloco)
                        @if (!empty($bloco['heading']))
                            <h5>{{ $bloco['heading'] }}</h5>
                        @endif

                        @if (!empty($bloco['paragrafo']))
                            <p>{!! $bloco['paragrafo'] !!}</p>
                        @endif

                        @if (!empty($bloco['lista']))
                            <ol>
                                @foreach ($bloco['lista'] as $item)
                                    <li>{!! $item !!}</li>
                                @endforeach
                            </ol>
                        @endif
                    @endforeach
                </div>

                @if (!empty($artigo['cta_url']))
                    <a href="{{ $artigo['cta_url'] }}" class="btn custom-btn custom-btn-primary mt-2">
                        <i class="fas fa-arrow-right mr-1"></i> {{ $artigo['cta_label'] }}
                    </a>
                @endif

                <div class="help-feedback">
                    <p class="text-muted mb-3">Este artigo foi útil?</p>
                    <div id="help-feedback-buttons">
                        <button type="button" class="btn btn-outline-success help-feedback-btn" onclick="responderFeedback(true)">
                            <i class="far fa-thumbs-up mr-1"></i> Sim
                        </button>
                        <button type="button" class="btn btn-outline-danger help-feedback-btn" onclick="responderFeedback(false)">
                            <i class="far fa-thumbs-down mr-1"></i> Não
                        </button>
                    </div>
                    <p id="help-feedback-thanks" class="text-muted mb-0 mt-3" style="display: none;">
                        <i class="fas fa-check-circle text-success mr-1"></i> Obrigado pelo seu feedback!
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card card-main" style="padding: 20px;">
                <h6 class="text-uppercase text-muted mb-2" style="font-size: 0.75rem; font-weight: 700; letter-spacing: .5px;">
                    Nesta categoria
                </h6>
                <a href="{{ route('ajuda.categoria', $categoria['slug']) }}" class="help-sidebar-nav-item">
                    <i class="{{ $categoria['icone'] }} mr-2"></i>{{ $categoria['titulo'] }}
                </a>
                @foreach ($relacionados as $relacionado)
                    <a href="{{ route('ajuda.artigo', $relacionado['slug']) }}" class="help-sidebar-nav-item">
                        {{ $relacionado['titulo'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    function responderFeedback(util) {
        document.getElementById('help-feedback-buttons').style.display = 'none';
        document.getElementById('help-feedback-thanks').style.display = 'block';
    }
</script>
@endpush
