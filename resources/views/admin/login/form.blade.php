@extends('layouts.default')

@section('content')
    <div class="container">
        <div class="login-box">
            <div class="login-logo">
                <a href=""></a>
            </div>
            <div class="login-box-body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <form action="{{ route('login') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="inputEmail" class="sr-only">Email</label>
                            <input type="email" name="email" id="inputEmail" class="form-control" placeholder="tu correo electronico">
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="sr-only">Password</label>
                            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Introduce tu contraseña">
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="remember-me"> Recuerdame
                            </label>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
                    </form>
            </div>
        </div>
    </div>
@endsection