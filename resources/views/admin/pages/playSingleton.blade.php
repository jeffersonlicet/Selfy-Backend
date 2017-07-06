@extends('admin.layouts.default')
@section("content")
    @php($status = [config('constants.DEV_CHALLENGE_STATUS.active') =>'Active', config('constants.DEV_CHALLENGE_STATUS.disabled') => 'Disabled'])
    @if($message)
    <div class="toast-message" data-title="{{$message->title}}" data-text="{{$message->body}}" data-type="{{$message->type}}" data-duration="{{$message->duration}}"></div>
    @endif

    <div class="section-title">
        <h2>#{{ $challenge->challenge_id }} {{ $challenge->Object->play_title }} <a data-status="{{ $challenge->status }}" data-id="{{ $challenge->challenge_id}}" href="#" onclick="window.challenge.toggle(this)"><span id="state-label-{{$challenge->challenge_id}}" class="label @if($challenge->status == config('constants.DEV_CHALLENGE_STATUS.active')) label-success @else label-danger @endif">{{ $status[$challenge->status]  }}</span></a></h2>
        <h4>{{ $challenge->Object->play_description  }}</h4>
    </div>

    <div class="container-fluid">
        <br />
        <div class="col-md-12">
            {!! Form::open(['route' => ['UpdatePlaySingleton', $challenge->challenge_id]]) !!}
            <h3>Title</h3>
            <input id="play_id" type="hidden" value="{{ $challenge->object->play_id }}">
            <div class="form-group label-floating">
                <label class="control-label" for="play_title">The play title</label>
                <input class="form-control" value="{{ $challenge->object->play_title }}" name="play_title" id="play_title" type="text" required autofocus>
            </div>

            <h3>Description</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_description">The play description</label>
                <input class="form-control" value="{{ $challenge->object->play_description }}" name="play_description" id="play_description" type="text" required>
            </div>

            <h3>Sample/s</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_sample">The play sample/s image/s</label>
                <input class="form-control" value="{{ $challenge->object->play_sample }}" name="play_sample" id="play_sample" type="text" required>
            </div>

            <h3>Play thumb</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="play_thumb">The play thumb image</label>
                <input class="form-control" value="{{ $challenge->object->play_thumb }}" name="play_thumb" id="play_thumb" type="text" required>
            </div>

            <button class="btn btn-raised btn-primary" type="submit">Save</button>
            {!!   Form::close() !!}
            <hr />
            <a href="{{ action('Admin\AdminController@managePlayObjects', $challenge->challenge_id) }}" class="btn btn-raised btn-primary">Manage Objects</a>
            <hr />
            <h3>Hashtag</h3>
           @if($challenge->Object->hashtag != null) <a href="#"><h4>#{{$challenge->Object->hashtag->hashtag_text}}</h4></a>@endif

            <a class="btn btn-raised btn-primary" data-toggle="modal" data-target="#changePlayHashtagModal">Change</a>
            <a class="btn btn-raised btn-info" data-toggle="modal" data-target="#createHashtagModal">New</a>
        </div>
    </div>
@stop