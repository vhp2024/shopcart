<div class="{{ Arr::get( $field, 'input_wrapper_class', config('app_settings.input_wrapper_class', 'form-group')) }} {{ $errors->has($field['name']) ? Arr::get( $field, 'input_error_class', config('app_settings.input_error_class', 'has-danger')) : '' }}">
    @include('zaco-base::components.fields._label')

    {{ $slot }}

    @include('zaco-base::components.fields._hint')
</div>
