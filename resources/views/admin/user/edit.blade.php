@extends('layouts.withsidebar')
@section('pageTitle')
    {{isset($pageTitle) ? $pageTitle : 'Users'}}
@endsection
@section('content')

    <div class="container-fluid admin">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{trans('selfy-admin.editUserTitle').' '.$user->name}}</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-xs-12 col-lg-offset-1 col-md-offset-1">
                        <div class="row">
                            {!! Form::model($user, []) !!}
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('name', trans('selfy-admin.userName')) !!}
                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                    @if($errors->has('name'))
                                        <span class="text-danger">{{$errors->first('name')}}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::label('email', trans('selfy-admin.userEmail')) !!}
                                    {!! Form::text('email', null, ['class' => 'form-control']) !!}
                                    @if($errors->has('email'))
                                        <span class="text-danger">{{$errors->first('email')}}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::submit(trans('selfy-admin.save'), ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('name', trans('selfy-admin.userRoles')) !!}
                                    {!! Form::select('roles[]', $roles, $user->getRolesForSelect(), ['class' => 'form-control selectBootstrap', 'multiple' => 'multiple']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-lg-6">
                                <h2>{{ trans('selfy-admin.resetPasswordTitle') }}</h2>
                                {!! Form::open(['route' => ['updatePassword', $user->id]]) !!}
                                <div class="form-group">
                                    {!! Form::label('password', trans('selfy-admin.newPassword')) !!}
                                    {!! Form::password('password', ['class' => 'form-control']) !!}
                                    @if($errors->has('password'))
                                        <span class="text-danger">{{$errors->first('password')}}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::label('password_confirmation', trans('selfy-admin.passwordConfirmation')) !!}
                                    {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                                    @if($errors->has('password_confirmation'))
                                        <span class="text-danger">{{$errors->first('password_confirmation')}}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::submit(trans('selfy-admin.update'), ['class' => 'btn btn-primary']) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection