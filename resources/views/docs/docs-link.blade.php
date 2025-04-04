@can('create', \App\Models\PersonalAccessToken::class)
    <div class="alert alert-info">
        {{ __('The available API endpoints are described in detail in our documentation.') }}
        <a href="{{ route('api-docs.index') }}" class="alert-link">
            {{ __('View API documentation') }}
        </a>
    </div>
@endcan
