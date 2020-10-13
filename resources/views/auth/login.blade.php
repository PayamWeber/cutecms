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
                    <form class="form-horizontal form-simple" action="{{ route('login') }}" method="post">
                        {{ csrf_field() }}
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
                                   placeholder="{{ lang('Password') }}" value="{{ old('password') }}" required>
                            <div class="form-control-position">
                                <i class="la la-key"></i>
                            </div>
                        </fieldset>
                        <img src="{{ url('captcha') }}">
                        <fieldset class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control form-control-lg input-lg" id="user-password"
                                   name="captcha"
                                   placeholder="{{ lang('Captcha') }}" value="{{ old('captcha') }}" required>
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
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info btn-lg btn-block">
                            <i class="ft-unlock"></i> {{ lang('Login') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection