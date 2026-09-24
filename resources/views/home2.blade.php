@extends('adminlte::page')

@section('title', 'Início')

@php
    $agora = now()->locale('pt_BR');
    $hora = (int) $agora->format('H');
    $saudacao = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');
    $primeiroNome = explode(' ', trim(auth()->user()->name))[0];
@endphp

@section('content')
    {{-- Boas-vindas --}}
    <div class="home-hero">
        <div>
            <div class="home-hero-date">{{ $agora->translatedFormat('l, d \d\e F \d\e Y') }}</div>
            <h1>{{ $saudacao }}, {{ $primeiroNome }}!</h1>
            <p>Que bom te ver por aqui. Escolha abaixo o que deseja fazer agora.</p>
        </div>
        <div class="home-hero-actions">
            <a href="{{ route('ajuda.index') }}" class="btn btn-light"><i class="fas fa-life-ring mr-1"></i> Central de ajuda</a>
        </div>
    </div>

    <div class="home-section-title">
        <h5>Acesso rápido</h5>
    </div>
    <x-home-atalhos />

    <div class="row">
        <div class="col-md-6 mb-4">
            <a href="{{ route('ajuda.index') }}" class="quick-card h-100">
                <span class="stat-icon bg-info-soft"><i class="fas fa-question-circle"></i></span>
                <span class="quick-card-text">
                    <strong>Precisa de ajuda?</strong>
                    <small>Tutoriais passo a passo para emitir e cadastrar</small>
                </span>
                <i class="fas fa-chevron-right quick-card-arrow"></i>
            </a>
        </div>
        <div class="col-md-6 mb-4">
            <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Preciso+de+suporte+no+SpeedNFE." target="_blank" rel="noopener" class="quick-card h-100">
                <span class="stat-icon bg-success-soft"><i class="fab fa-whatsapp"></i></span>
                <span class="quick-card-text">
                    <strong>Falar com o suporte</strong>
                    <small>Atendimento humano via WhatsApp</small>
                </span>
                <i class="fas fa-chevron-right quick-card-arrow"></i>
            </a>
        </div>
    </div>
@stop
