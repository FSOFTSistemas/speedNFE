@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content')

    @livewire('edit-pedido', ["pedido" => $pedido])

@endsection