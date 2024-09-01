@extends('zaco-base::auth.layout')

@section('content')
    <div class="col-md-6">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
                        <h4 class="text-center mb-4">{{ __('auth.reset_new_password') }}</h4>
                        <form method="POST" action="{{ route('Auth::postResetNewPassword')}}">
                            @csrf
                            <div class="form-group">
                                <label class="mb-1">
                                    <strong>{{ __('auth.new_password') }}</strong>
                                </label>
                                <input type="password" class="form-control" name="new_password" placeholder="{{ __('auth.new_password') }}" value="{{ old('new_password') }}">
                                @error('new_password')
                                    <div class="invalid-feedback" style="display: block;">{{ $errors->first('new_password') }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="mb-1">
                                    <strong>{{ __('auth.confirm_new_password') }}</strong>
                                </label>
                                <input type="password" class="form-control" name="confirm_new_password" placeholder="{{ __('auth.confirm_new_password') }}" value="{{ old('confirm_new_password') }}">
                                @error('confirm_new_password')
                                    <div class="invalid-feedback" style="display: block;">{{ $errors->first('confirm_new_password') }}</div>
                                @enderror
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-block">{{__('auth.reset_new_password')}}</button>
                            </div>
                            <input type="hidden" name="user_token" value="{{old('user_token', $user_token)}}" />
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
