@extends('zaco-base::auth.layout')

@section('content')
    <div class="card mb-0">
        <div class="card-body">
            @include('zaco-base::auth.includes.logo')
            <h4 class="card-title mb-1">{{ __('auth.register') }} 👋</h4>
            <form method="POST" action="{{ route('Auth::postRegister')}}">
                @csrf
                <div class="mb-1">
                    <label for="login-email" class="form-label">{{__('auth.name')}}</label>
                    <input type="text" class="form-control" name="name" placeholder="{{__('auth.name')}}" value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback" style="display: block;">{{ $errors->first('name') }}</div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label for="login-email" class="form-label">{{__('auth.username')}}</label>
                    <input type="text" class="form-control" name="username" placeholder="{{__('auth.username')}}" value="{{ old('username') }}">
                    @error('username')
                        <div class="invalid-feedback" style="display: block;">{{ $errors->first('username') }}</div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label for="login-email" class="form-label">{{__('auth.email')}}</label>
                    <input type="text" class="form-control" name="email" placeholder="hello@example.com" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback" style="display: block;">{{ $errors->first('email') }}</div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label for="login-email" class="form-label">{{__('auth.password')}}</label>
                    <input type="password" class="form-control" name="password" value="">
                    @error('password')
                        <div class="invalid-feedback" style="display: block;">{{ $errors->first('password') }}</div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label for="login-email" class="form-label">{{__('auth.confirm_password')}}</label>
                    <input type="password" class="form-control" name="confirmation_password" value="">
                    @error('confirmation_password')
                        <div class="invalid-feedback" style="display: block;">{{ $errors->first('confirmation_password') }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100" tabindex="4">{{__('auth.sign_up')}}</button>
            </form>
            <p class="text-center mt-2">
                <span>{{__('auth.already_have_an_account')}}</span>
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
@endsection
