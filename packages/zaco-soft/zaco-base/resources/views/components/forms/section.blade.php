<div class="{{ Arr::get($form, 'section_class', config('app_settings.section_class', 'card')) }} section-{{ Str::slug($form['title']) }}">
    <div class="{{ Arr::get($form, 'section_heading_class', config('app_settings.section_heading_class', 'card-header')) }}">
        <i class="{{ Arr::get($form, 'icon', 'glyphicon glyphicon-flash') }}"></i>
        {{ __($form['title']) }}
    </div>

    @if( $desc = Arr::get($form, 'descriptions') )
        <div class="pb-0 {{ config('app_settings.section_body_class', Arr::get($form, 'section_body_class', 'card-body')) }}">
            <p class="text-muted mb-0 ">{{ $desc }}</p>
        </div>
    @endif

    {{ $slot }}
</div>
