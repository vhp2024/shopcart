@php
    $fieldName = isset($field['multiple']) ? $field['name'].'[]' : $field['name'];
    $class = '';
    if(isset($field['class'])) $class = $field['class'];
@endphp

<select name="{{ $fieldName }}"
        class="form-control select2 {{$class}}"
        @if(isset($field['multi'])) multiple @endif
        @if( $styleAttr = Arr::get($field, 'style')) style="{{ $styleAttr }}" @endif
        id="{{ $field['name'] }}">
        @foreach(Arr::get($field, 'options', []) as $item)
                <option value="{{ $item['id'] }}" @if( old($field['name'], $field['value']) == $item['id'] ) selected @endif>
                        {{ $item['text'] }}
                </option>
        @endforeach
</select>
