<form method="post" action="{{ route($form['route']) }}" class="form-horizontal mb-3" enctype="multipart/form-data" role="form" id="{{ $form['frmId'] ?? 'frm'}}">
    {!! csrf_field() !!}

    @if( isset($inputs) )
        @component('zaco-base::components.forms.section', compact('form', 'inputs'))
            <div class="card-body">
                @foreach($inputs as $field)
                    @includeIf('zaco-base::components.fields.' . $field['type'] )
                @endforeach
            </div>
        @endcomponent
    @endif

    <div class="row m-b-md">
        <div class="col-md-12">
            <button class="btn-primary btn" type="{{ $form['btnSaveType'] ?? ''}}" id="{{ $form['btnSaveId'] ?? 'btn-save'}}">
                {{ __('common.save') }}
            </button>
        </div>
    </div>
</form>
