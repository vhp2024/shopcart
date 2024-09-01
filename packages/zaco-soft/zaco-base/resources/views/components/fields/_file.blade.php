@component('zaco-base::components.forms.input_group', compact('field'))

    <br>
    <input type="file"
           name="{{ $field['name'] }}"
           @if( $placeholder = Arr::get($field, 'placeholder') )
           placeholder="{{ $placeholder }}"
           @endif
           class="{{ Arr::get( $field, 'class') }} {{ $errors->has($field['name']) ? 'is-invalid' : '' }}"
           @if( $styleAttr = Arr::get($field, 'style')) style="{{ $styleAttr }}" @endif
           id="{{ Arr::get($field, 'name') }}"
    >


@endcomponent
