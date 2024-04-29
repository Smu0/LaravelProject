@extends('layouts.suggestion')
@section('title', $viewData["title"])
@section('subtitle', $viewData["subtitle"])
@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Articolo aggiunto al carrello</h2>
        </div>
        <div class="card-body">
            <div class ="col-md-4">
                <img src="{{asset('/storage/'.$viewData['product']->getImage()) }}" class = "img-fluid rounded-start"> 
            </div> 
            <div class="col-md-8">
                <h5 class="card-text" style="text-align:left"><small class="text-muted"><b>Nome:</b> {{$viewData["product"]["name"]}}</small></h5>
                <p class="card-text" style="text-align:left"><small class="text-muted"><b>Prezzo:</b> {{$viewData["product"]["price"] }} €</small></p>
                <p class="card-text"style="text-align:left"><small class="text-muted"><b>Quantità:</b> {{$viewData["qta"]}}</small></p>
                <p class="card-text" style="text-align:left"><small class="text-muted"><b>Specie:</b> {{$viewData["product"]["specie"] }}</small></p>
                <p class="card-text" style="text-align:left"><small class="text-muted"><b>Ambiente:</b> {{$viewData["product"]["ambiente"] }}</small></p>
                <p class="card-text"style="text-align:left"><small class="text-muted"><b>Descrizione:</b> {{$viewData["product"]["description"]}}</small></p>
            </div>
        </div>
    </div>
    <form method="GET" action="{{route('cart.index')}}">
        @csrf
        <button type = "submit" class="btn bg-primary text-white mb-2" style = "width: 100%">Vai al carrello</button>
    </form>


@endsection