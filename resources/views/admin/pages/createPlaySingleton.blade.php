@extends('admin.layouts.default')
@section("content")
    @php($status = [config('constants.DEV_CHALLENGE_STATUS.active') =>'Active', config('constants.DEV_CHALLENGE_STATUS.disabled') => 'Disabled'])

    <div class="section-title">
        <h2>Create Play challenge</h2>
        <h4>Here you can create a new Play challenge</h4>
    </div>

    <div class="container-fluid">
        <br />
        <div class="col-md-12">
            {!! Form::open(['route' => ['CreatePlaySingleton']]) !!}
            <h3>Title</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_title">The play title</label>
                <input class="form-control"  name="play_title" id="play_title" type="text" required autofocus>
            </div>

            <h3>Description</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_description">The play description</label>
                <input class="form-control" name="play_description" id="play_description" type="text" required>
            </div>

            <h3>Samples</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_sample">The play samples image/s</label>
                <input class="form-control" name="play_sample" id="play_sample" type="text" required>
            </div>

            <h3>Play thumb</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_thumb">The play thumb image</label>
                <input class="form-control" name="play_thumb" id="play_thumb" type="text" required>
            </div>

            <button class="btn btn-raised btn-primary" type="submit">Save</button>
            {!!   Form::close() !!}
        </div>
    </div>
@stop