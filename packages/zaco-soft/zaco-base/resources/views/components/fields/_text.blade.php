@component('zaco-base::components.forms.input_group', compact('field'))
    <?php
if (!isset($class)) {
    $class = '';
}

$required = false;
if (isset($field['required']) && $field['required']) {
    $class .= ' required';
    $required = true;
}
?>
    @if($required)
        <span class="txt-red">*</span>
    @endif
    <input type="{{ $field['type'] }}"
           name="{{ $field['name'] }}"
           @if( $placeholder = Arr::get($field, 'placeholder') )
           placeholder="{{ $placeholder }}"
           @endif
           value="{{ old($field['name'], $field['value']) }}"
           class="form-control {{ $errors->has($field['name']) ? 'is-invalid' : '' }}  {{ $class }}"
           @if( $styleAttr = Arr::get($field, 'style')) style="{{ $styleAttr }}" @endif
           @if( $maxAttr = Arr::get($field, 'max')) max="{{ $maxAttr }}" @endif
           @if( $minAttr = Arr::get($field, 'min')) min="{{ $minAttr }}" @endif
           id="{{ Arr::get($field, 'name') }}"
    >
    @if( $append = Arr::get($field, 'append'))
        <span>{{ $append }} </span>
    @endif
@endcomponent
