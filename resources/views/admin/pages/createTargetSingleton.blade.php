@extends('admin.layouts.default')
@section("content")
    @php($status = [config('constants.DEV_CHALLENGE_STATUS.active') =>'Active', config('constants.DEV_CHALLENGE_STATUS.disabled') => 'Disabled'])

    <div class="section-title">
        <h2>Nueva palabra clave</h2>
        <h4>Crea una nueva palabra clave</h4>
    </div>

    <div class="container-fluid">
        <br />
        <div class="col-md-12">
            {!! Form::open(['route' => ['MeliCreateTargetPost']]) !!}
            <h3>Title</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="name">Palabra clave</label>
                <input class="form-control"  name="name" id="name" type="text" required autofocus>
            </div>

            <button class="btn btn-raised btn-primary" type="submit">Save</button>
            {!!   Form::close() !!}
        </div>
    </div>
@stop