<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('styles')
</head>
<body>
    @include('layouts.header')

    <div class="container-fluid">
        @hasSection('breadcrumbs')
            <x-bs::breadcrumb container-class="mx-5 mt-3" class="bg-light rounded-pill p-2">
                <x-bs::breadcrumb.item href="{{ route('dashboard') }}">{{ __('Dashboard') }}</x-bs::breadcrumb.item>
                @yield('breadcrumbs')
            </x-bs::breadcrumb>
        @endif

        <main class="mx-5 my-3">
            @include('layouts.alerts')

            @section('main')
                @hasSection('headline-buttons')
                    <div class="hstack gap-3">
                        @section('headline')
                            <h1>@yield('title')</h1>
                        @show
                        <div class="ms-auto text-nowrap">
                            @section('headline-buttons')
                            @show
                        </div>
                    </div>
                @else
                    @section('headline')
                        <h1>@yield('title')</h1>
                    @show
                @endif

                @yield('content')
            @show
        </main>
    </div>

    @include('layouts.footer')

    @section('scripts')
        <script src="{{ mix('/lib/bootstrap.bundle.min.js') }}"></script>
        @stack('scripts')
    @show
</body>
</html>
