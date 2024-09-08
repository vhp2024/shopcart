@extends('zaco-base::auth.layout')

@section('content')
    <!-- Login basic -->
    <div class="card mb-0">
        <div class="card-body">
            @include('zaco-base::auth.includes.logo')
            <h4 class="card-title mb-1">{{ __('auth.login') }} 👋</h4>
            <form method="POST" action="{{ route('Auth::postLogin')}}">
                @csrf
                <div class="mb-1">
                    <label for="login-email" class="form-label">{{ __('auth.username_email') }}</label>
                    <input type="text" class="form-control" name="username_email" placeholder="{{ __('auth.username_email') }}" value="{{ old('username_email') }}">
                    @error('username_email')
                        <div class="invalid-feedback" style="display: block;">{{ $errors->first('username_email') }}</div>
                    @enderror
                </div>
                <div class="mb-1">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="login-password">Password</label>
                        <a href="auth-forgot-password-basic.html">
                            <small>Forgot Password?</small>
                        </a>
                    </div>
                    <div class="input-group input-group-merge form-password-toggle">
                        <input type="password" name="password" class="form-control form-control-merge" placeholder="{{ __('auth.password') }}" value="">
                        <span class="input-group-text cursor-pointer">
                            <i data-feather="eye"></i>
                        </span>
                        @error('password')
                            <div class="invalid-feedback" style="display: block;">{{ $errors->first('password') }}</div>
                        @enderror
                    </div>
                </div>
                <!-- <div class="mb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember-me" tabindex="3" />
                        <label class="form-check-label" for="remember-me"> Remember Me </label>
                    </div>
                </div> -->
                @if(Request::get('last-url'))
                    <input type="hidden" name="last-url" value="{{Request::get('last-url')}}">
                @endif
                <button type="submit" class="btn btn-primary w-100" tabindex="4">{{ __('auth.sign_me_in') }}</button>
            </form>
            <p class="text-center mt-2">
                <span>New on our platform?</span>
                <a href="{{ route('Auth::register') }}">
                    <span>{{ __('auth.sign_up') }}</span>
                </a>
            </p>
            <!-- <div class="divider my-2">
                <div class="divider-text">or</div>
            </div>
            <div class="auth-footer-btn d-flex justify-content-center">
                <a href="#" class="btn btn-facebook">
                    <i data-feather="facebook"></i>
                </a>
                <a href="#" class="btn btn-twitter white">
                    <i data-feather="twitter"></i>
                </a>
                <a href="#" class="btn btn-google">
                    <i data-feather="mail"></i>
                </a>
                <a href="#" class="btn btn-github">
                    <i data-feather="github"></i>
                </a>
            </div> -->
        </div>
    </div>
    <!-- /Login basic -->
@endsection
