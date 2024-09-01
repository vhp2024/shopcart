@if( $label = Arr::get($field, 'label') )
    <label for="{{ Arr::get($field, 'name') }}">{{ __($label) }}</label>
@endif
