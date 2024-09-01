@extends('zaco-base::layout.split-content')

@section('sub-content-left')
    @component('zaco-base::components.forms.form-content', compact('form', 'inputs'))
    @endcomponent
@endsection

@section('sub-content-right')
    @component('zaco-base::admin.components.menus', ['menus' => $menus, 'route' => 'Setting::index'])
    @endcomponent
@endsection
