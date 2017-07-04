@extends('admin.layouts.default')
@section("content")
    <div class="section-title">
        <h2>Play management</h2>
        <h4>Here you can create and manage the existing Play challenges.</h4>

    </div>
    <a href="javascript:void(0)" class="btn btn-info btn-fab floating-button"><i class="material-icons">add</i></a>
    <div class="container-fluid">
        <div class="col-md-12">
            <table class="table table-striped table-hover ">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Sample</th>
                    <th>Description</th>
                    <th>Completed</th>
                    <th>Hashtag</th>
                    <th>Manage</th>
                </tr>
                </thead>
                <tbody>

                @php($status = [config('constants.DEV_CHALLENGE_STATUS.active') =>'Active', config('constants.DEV_CHALLENGE_STATUS.disabled') => 'Disabled'])
                @foreach($challenges as $challenge)
                    <tr>
                        <td>{{ $challenge->challenge_id }}</td>
                        <td title="{{ $challenge->object->play_title }}" data-toggle="tooltip">{{ str_limit($challenge->object->play_title, 20, '...') }}</td>
                        <td><span class="label @if($challenge->status == config('constants.DEV_CHALLENGE_STATUS.active')) label-success @else label-danger @endif">{{ $status[$challenge->status]  }}</span></td>
                        <td><a href="javascript:void(0)" data-toggle="modal" data-target="#imagePreviewModal" onclick="window.mImage.open(this)" data-href="{{ $challenge->object->play_sample }}"><i class="material-icons">photo</i></a></td>
                        <td title="{{ $challenge->object->play_description }}" data-toggle="tooltip">{{ str_limit($challenge->object->play_description, 41, '...') }}</td>
                        <td>{{ $challenge->completed_count }}</td>
                        <td><a href="#">#{{ $challenge->object->hashtag->hashtag_text }}</a></td>
                        <td><a href="{{  action('Admin\AdminController@playSingleton', ['playId' => $challenge->challenge_id]) }}"><i class="material-icons">settings</i></a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $challenges->links() }}
        </div>
    </div>
@stop