<div class="row justify-content-center">
    <div class="col-sm-8 col-lg-6">
        <div class="card border-primary border-2">
            <div class="card-header">@yield('title')</div>
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
