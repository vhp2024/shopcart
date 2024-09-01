<ul class="list-group mb-3">
    @if(count($menus))
        @foreach($menus as $menu)
            @php
                if(!isset($route_params))
                {
                    $href =  route($route, $menu['code']);
                }
                else
                {
                    $href =  route($route, $route_params + ['code' => $menu['code']]);
                }
                
                $active = $href == Request::url();
            @endphp
            <li class="list-group-item d-flex justify-content-between @if($active) active @else lh-condensed @endif">
                <a href="{{ $href }}" class="@if($active) text-white @endif">
                    <div>
                        <h6 class="my-0 @if($active) text-white @endif">{{ __($menu['title']) }}</h6>
                    </div>
                </a>
            </li>
        @endforeach
    @endif
</ul>