@extends('zaco-base::layout.split-content')

@section('sub-content-right')
@component('zaco-base::admin.components.menus', ['menus' => $menus, 'route' => 'Profile::index'])
    @endcomponent
@endsection

@section('sub-content-left')
    @if(!Auth::user()->two_factor_secret)
    <div class="visible-print text-center qr-code-center">
        {!! QrCode::size(200)->generate($others['google2fa']['qr_code_url']); !!}
        <p>Scan me to return to the original page.</p>
        <p>Secret key: <b>{{$others['google2fa']['secret_key']}}</b></p>
    </div>
    @else
    <div class="text-center ">
       Remove
    </div>
    @endif
    <form method="post" action="{{ route($form['route']) }}" class="form-horizontal mb-3" enctype="multipart/form-data" role="form">
        {!! csrf_field() !!}

        <div class="row justify-content-start">
            <div class="col-md-6 offset-md-3">
                <input type="text" name="secret_code" class="form-control number" >
                @error('secret_code')
                    <div class="invalid-feedback" style="display: block;">{{ $errors->first('secret_code') }}</div>
                @enderror
            </div>
        </div>

        <div class="row m-b-md">
            <div class="col-md-12 text-center">
                <button class="btn-primary btn">
                    {{ __('common.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection
