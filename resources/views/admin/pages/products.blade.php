@extends('admin.layouts.default')
@section("content")
    <div class="section-title">
        <h2>Productos</h2>
        <h4>Lista de productos</h4>
    </div>

    <div class="container-fluid">
        @foreach($products as $product)
        <div class="panel panel-default col-md-3" style="margin:8px">
            <div class="panel-body">
                <p>{{$product->title}}</p>
                <img src="{{$product->thumbnail}}">
            </div>
        </div>
        @endforeach
        <hr />
            {{ $products->links() }}
    </div>
@stop