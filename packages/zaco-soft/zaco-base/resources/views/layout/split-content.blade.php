@extends('zaco-base::layout.template')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 order-md-2 mb-4">
                        @yield('sub-content-right')
                    </div>
                    <div class="col-md-8 order-md-1">
                        @yield('sub-content-left')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
