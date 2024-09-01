@extends('zaco-base::auth.layout')

@section('content')
    <div class="col-md-6">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
                        <h4 class="text-center mb-4">{{ __('auth.forget_password') }}</h4>
                        <form method="POST" action="{{ route('Auth::postForgetPassword')}}">
                            @csrf
                            <div class="form-group">
                                <label class="mb-1">
                                    <strong>{{ __('auth.username_or_email') }}</strong>
                                </label>
                                <input type="text" class="form-control" name="username_or_email" placeholder="{{ __('auth.username_or_email') }}" value="{{ old('username_or_email') }}">
                                @error('username_or_email')
                                    <div class="invalid-feedback" style="display: block;">{{ $errors->first('username_or_email') }}</div>
                                @enderror
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-block">{{__('auth.forget_password')}}</button>
                            </div>
                        </form>
                        <div class="new-account mt-3">
                            <p>{{__('auth.already_have_an_account')}} <a class="text-primary" href="{{ route('Auth::login') }}">{{__('auth.sign_in')}}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
