@extends('admin.layouts.default')
@section("content")
    @php($status = [config('constants.DEV_CHALLENGE_STATUS.active') =>'Active', config('constants.DEV_CHALLENGE_STATUS.disabled') => 'Disabled'])
    <div class="toast-message" data-title="Hey!" data-text="Play challenge updated" data-type="success" data-duration="5000"></div>
    <div class="section-title">
        <h2>#204 Sunglasses challenge <a data-status="{{ $challenge->status }}" data-id="{{ $challenge->challenge_id}}" href="#" onclick="window.challenge.toggle(this)"><span id="state-label" class="label @if($challenge->status == config('constants.DEV_CHALLENGE_STATUS.active')) label-success @else label-danger @endif">{{ $status[$challenge->status]  }}</span></a></h2>
        <h4>Take a selfie using sunglasses</h4>
    </div>

    <div class="container-fluid">
        <br />
        <div class="col-md-12">
            {!! Form::open(['route' => ['UpdatePlaySingleton', 204]]) !!}
            <h3>Title</h3>

            <div class="form-group label-floating">
                <label class="control-label" for="play_title">The play title</label>
                <input class="form-control" value="{{ $challenge->object->play_title }}" name="play_title" id="play_title" type="text" required autofocus>
            </div>

            <h3>Description</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_description">The play description</label>
                <input class="form-control" value="{{ $challenge->object->play_description }}" name="play_description" id="play_description" type="text" required>
            </div>

            <h3>Sample</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_sample">The play sample image</label>
                <input class="form-control" value="{{ $challenge->object->play_sample }}" name="play_sample" id="play_sample" type="text" required>
            </div>

            <button class="btn btn-raised btn-primary" type="submit">Save</button>
            {!!   Form::close() !!}
            <hr />
            <h3>Hashtag</h3>
            <a href=""><h4>#Papito</h4></a>

            <div class="panel-footer">
                <a class="btn btn-raised btn-primary btn-sm">Change</a>
                <a class="btn btn-raised btn-info btn-sm">New</a>
            </div>
        </div>
    </div>
@stop