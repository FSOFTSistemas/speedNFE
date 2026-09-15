@extends('adminlte::page')

@section('content')
    @inertia('inertia-app')
@endsection

@section('adminlte_css')
    @inertiaHead
@endsection

@section('js')
    @routes
    @viteReactRefresh
    @vite(['resources/js/app.jsx'])
@endsection