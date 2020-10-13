@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends( 'layouts.auth' )
@section('content')
    <div class="col-md-4 col-10 box-shadow-2 p-0">
        <div class="card border-grey border-lighten-3 m-0">
            <div class="card-header border-0">
                <div class="card-title text-center">
                    <div class="p-1">
                        <img src="{{ admin_assets_url() }}/app-assets/images/logo/logo-dark.png" alt="branding logo">
                    </div>
                </div>
                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                    <span>{{ lang('Login to admin') }}</span>
                </h6>
            </div>
            <div class="card-content">
                <div class="col">
                    @include( 'layouts.admin.alert', ['show_errors' => true ] )
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-simple" action="{{ route('register') }}" method="post">
                        {{ csrf_field() }}
                        <fieldset class="form-group position-relative has-icon-left mb-0">
                            <input type="text" class="form-control form-control-lg input-lg" id="name"
                                   name="name"
                                   placeholder="{{ lang('User Name') }}"
                                   value="{{ old('name') }}"
                                   required>
                            <div class="form-control-position">
                                <i class="ft-user"></i>
                            </div>
                        </fieldset>
                        <fieldset class="form-group position-relative has-icon-left mb-0">
                            <input type="email" class="form-control form-control-lg input-lg" id="email"
                                   name="email"
                                   placeholder="{{ lang('Your Email') }}"
                                   value="{{ old('email') }}"
                                   required>
                            <div class="form-control-position">
                                <i class="ft-user"></i>
                            </div>
                        </fieldset>
                        <fieldset class="form-group position-relative has-icon-left">
                            <input type="password" class="form-control form-control-lg input-lg" id="user-password"
                                   name="password"
                                   placeholder="{{ lang('Password') }}" value="{{ old('email') }}" required>
                            <div class="form-control-position">
                                <i class="la la-key"></i>
                            </div>
                        </fieldset>
                        <div class="form-group row">
                            <div class="col-md-6 col-12 text-center text-md-left">
                                <fieldset>
                                    <input type="checkbox" id="remember-me" name="remember" class="chk-remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="remember-me"> {{ lang('Remember me') }}</label>
                                </fieldset>
                            </div>
                            <div class="col-md-6 col-12 text-center text-md-right">
                                <a href="{{ url('password/reset') }}" class="card-link">
                                    {{ lang('Forgot your password ?') }}
                                </a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info btn-lg btn-block">
                            <i class="ft-unlock"></i> {{ lang('Register') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-footer">
                <div class="">
                    <p class="float-sm-left text-center m-0">
                        <a href="{{ url('password/reset') }}" class="card-link">{{ lang('Recover Password') }}</a>
                    </p>
                    <p class="float-sm-right text-center m-0">
                        <a href="{{ url('register') }}" class="card-link">{{ lang('Sign Up') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection