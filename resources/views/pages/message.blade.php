@extends('layouts.default')

@section("content")
    <div class="center-block col-md-6 " style="float: none;margin-top: 40px;">
        <div class="panel panel-primary">
            <div class="panel-heading">{{$messageTitle}}</div>
            <div class="panel-body">
                {{$messageBody}}
            </div>
        </div>
    </div>
@stop