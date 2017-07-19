@extends('admin.layouts.default')
@section("content")
    <div class="section-title">
        <h2>Productos</h2>
        <h4>Lista de productos</h4>
    </div>
    <br />

    <div class="container-fluid">
        <div class="row col-md-offset-2">
            {{ $products->links() }}
        </div>

        <div class="row" style="margin-left:50px">
            @foreach($products as $product)
            <div class="panel panel-default col-md-3 productBox" style="margin:8px">
                <div class="panel-body">
                    <img src="{{ $product->thumbnail}}">
                    <a href="{{ $product->permalink }}" target="_blank" title="{{$product->title}}" data-toggle="tooltip"><h4>{{ str_limit($product->title, 25)}}</h4></a>
                    <h3>{{$product->price}} Bsf.</h3>

                </div>
            </div>
            @endforeach
        </div>
        <div class="row col-md-offset-2">
            {{ $products->links() }}
        </div>

    </div>
@stop