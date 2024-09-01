<?php

?>
<div class="form-check form-check-inline">
    <label class="form-check-label">
        <input class="form-check-input"
               id="{{ $field['name'] }}"
               type="radio"
               name="{{ $field['name'] }}"
               value="{{ $field['true_value']['value'] }}"
               @if($field['value'] == $field['true_value']['value']) checked @endif>
            {{ $field['true_value']['label'] }}
    </label>
</div>
<div class="form-check form-check-inline">
    <label class="form-check-label">
        <input class="form-check-input"
               type="radio"
               name="{{ $field['name'] }}"
               value="{{ $field['false_value']['value'] }}"
               @if($field['value'] == Arr::get($field, 'false_value', '0')) checked @endif>
            {{ $field['false_value']['label'] }}
    </label>
</div>
