@extends('layouts.main')
@section('pageTitle')
    {{trans('selfy-admin.LoginPageTitle')}}
@endsection
@section('content')
    <div class="container">
        <div class="login-box">
            <div class="login-logo">
                <a href="">{{trans('selfy-admin.LoginMessage')}}</a>
            </div>
            <div class="login-box-body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        {!! trans('selfy-admin.LoginErrorHeader') !!}<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {!! Form::open(['route' => 'SelfyAdminLoginPost']) !!}
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">{{trans('selfy-admin.LoginEmailLabel')}}</label>
                    <input type="email" name="email" id="inputEmail" class="form-control" placeholder="{{trans('selfy-admin.LoginEmailLabel')}}" required autofocus>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="sr-only">{{trans('selfy-admin.LoginPasswordLabel')}}</label>
                    <input type="password" name="password" id="inputPassword" class="form-control" placeholder="{{trans('selfy-admin.LoginPasswordLabel')}}" required>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="remember-me"> {{trans('selfy-admin.LoginRememberMe')}}
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">{{trans('selfy-admin.LoginSignIn')}}</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
