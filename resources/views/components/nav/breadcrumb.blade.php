@isset($href)
    <li class="breadcrumb-item">
        <a href="{{ $href }}">{{ $slot }}</a>
    </li>
@else
    @php
        /** @var \Illuminate\Support\HtmlString $slot */
    @endphp
    <li class="breadcrumb-item active" aria-current="page">
        @if($slot->isEmpty())
            @yield('title')
        @else
            {{ $slot }}
        @endif
    </li>
@endif
