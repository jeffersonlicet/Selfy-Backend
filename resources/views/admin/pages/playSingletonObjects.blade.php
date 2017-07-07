@extends('admin.layouts.default')
@section("content")
    @php($status = [config('constants.DEV_CHALLENGE_STATUS.active') =>'Active', config('constants.DEV_CHALLENGE_STATUS.disabled') => 'Disabled'])
    @if($message)
    <div class="toast-message" data-title="{{$message->title}}" data-text="{{$message->body}}" data-type="{{$message->type}}" data-duration="{{$message->duration}}"></div>
    @endif

    <div class="section-title">
        <h2>#{{ $challenge->challenge_id }} {{ $challenge->Object->play_title }} <a data-status="{{ $challenge->status }}" data-id="{{ $challenge->challenge_id}}" href="#" onclick="window.challenge.toggle(this)"><span id="state-label-{{$challenge->challenge_id}}" class="label @if($challenge->status == config('constants.DEV_CHALLENGE_STATUS.active')) label-success @else label-danger @endif">{{ $status[$challenge->status]  }}</span></a></h2>
        <h4>{{ $challenge->Object->play_description  }}</h4>
        <input type="hidden" id="play_id" value="{{$challenge->Object->play_id}}">
    </div>

    <div class="container-fluid">
        <br />
        @if($challenge->Object->Objects->count() > 0)
            <h3>&nbsp;&nbsp;CURRENT OBJECTS</h3>
            <div class="col-md-12">
                <table class="table table-striped table-hover ">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category Parent</th>
                        <th>Delete Association</th>
                        <th>Manage Object</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($challenge->Object->Objects as $obj)
                        <tr id="object_{{ $obj->ObjectCategory->category_id }}">
                            <td>{{ $obj->ObjectCategory->category_id }}</td>
                            <td>{{ $obj->ObjectCategory->Word->object_words }}</td>
                            <td>{{ $obj->ObjectCategory->Parent == null ? '-' : $obj->ObjectCategory->Parent->Word->object_words }}</td>
                            <td><a href="#" data-play="{{ $challenge->Object->play_id }}" data-toggle="modal" data-target="#loadingModal" data-object="{{ $obj->ObjectCategory->category_id }}" onclick="window.play.removeObject(this)"><i class="material-icons">delete</i></a></td>
                            <td><a href="#"><i class="material-icons">settings</i></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="col-md-6">
            <h3>CREATE OBJECT</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="object_name">Object name</label>
                <input class="form-control" name="object_name" id="object_name" type="text">
            </div>

            <div class="form-group label-floating">
                <label class="control-label" for="object_parent">Object parent</label>
                <input class="form-control" name="object_parent" id="object_parent" type="text">
            </div>
            <a href="#" onclick="window.play.createObject(this)" data-toggle="modal" data-target="#loadingModal" class="btn btn-raised btn-primary">Save</a>
        </div>

        <div class="col-md-6">
            <h3>ADD OBJECT</h3>
            <div class="form-group label-floating">
                <label class="control-label" for="object_id">Object #ID</label>
                <input class="form-control" name="object_id" id="object_id" type="text">
            </div>

            <a href="#" onclick="window.play.appendObject(this)" data-toggle="modal" data-target="#loadingModal" class="btn btn-raised btn-primary">Save</a>
        </div>

        @if(!isset($objectsGen))
        <div class="col-md-12">
            <h3>GENERATE OBJECTS</h3>
           {!! Form::open(['action' => ['Admin\AdminController@playGenerateObjects', $challenge->Object->play_id]]) !!}
            <div class="form-group label-floating">
                <label class="control-label" for="image_sample">Image Sample [, image sample]</label>
                <input class="form-control" name="image_sample" id="image_sample" type="text" value="{{$challenge->Object->play_sample}}" required>
            </div>
            <button type="submit" class="btn btn-raised btn-danger" data-toggle="modal" data-target="#loadingModal">Generate</button>
            {!! Form::close() !!}
        </div>
        @else

            <div class="col-md-12">
                <h3>OBJECTS GENERATED</h3>
                <table class="table table-striped table-hover ">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Associated</th>
                        <th>Append</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($objectsGen as $obj)
                        <tr class="tr">
                            <td>{{ $obj->category_id }}</td>
                            <td>{{ $obj->Word->object_words }}</td>
                            <td class="associated">@if($obj->associated) <span class="label label-success">Yes</span>@else <span class="label label-danger">No</span> @endif</td>
                            <td class="associate">@if(!$obj->associated) <a href="javascript:void(0)" onclick="window.play.appendObjectGenerated(this)" data-toggle="modal" data-target="#loadingModal" data-id="{{ $obj->category_id }}"><i class="material-icons">link</i></a> @else - @endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
@stop