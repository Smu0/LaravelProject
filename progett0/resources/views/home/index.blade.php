@extends('layouts.app')
@section('title',$viewData["title"]) 
@section('content')
<div class = "row">
    <div class = "col-md-6 col-lg-4 mb-2">
        <img src = "{{ asset('/storage/img1.jpg')}}" class="img-fluid">
    </div>

    <div class = "col-md-6 col-lg-4 mb-2">
        <img src = "{{ asset('/storage/img2.jpg')}}" class="img-fluid">
    </div>

    <div class = "col-md-6 col-lg-4 mb-2">
        <img src = "{{ asset('/storage/img3.jpg')}}" class="img-fluid">
    </div>
</div>
@endsection
