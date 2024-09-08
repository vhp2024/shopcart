@extends('zaco-base::layout.split-content')

@section('sub-content-left')
    @component('zaco-base::components.forms.form-content', compact('form', 'inputs'))
    @if($code == 'security')
        @includeIf('zaco-base::profiles.security', compact('others') )
    @endif
    @endcomponent
@endsection

@section('sub-content-right')
    @component('zaco-base::admin.components.menus', ['menus' => $menus, 'route' => 'Profile::index'])
    @endcomponent
@endsection
