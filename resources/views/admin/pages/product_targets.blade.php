@extends('admin.layouts.default')
@section("content")
    <div class="section-title">
        <h2>Productos</h2>
        <h4>Lista de productos</h4>
    </div>

    <div class="container-fluid">
        <table class="table table-striped table-hover ">
            <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
            </tr>
            </thead>
            <tbody>

        @foreach($targets as $target)
            <tr>
                <td>{{ $target->id }}</td>
                <td><a href="{{action('Admin\AdminController@meliProducts', $target->id)}}" target="_blank">{{ $target->name }}</a></td>
            </tr>
        @endforeach
            </tbody>
        </table>
        {{ $targets->links() }}
    </div>
@stop