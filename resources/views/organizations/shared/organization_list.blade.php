@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Organization[] $organizations */
    /** @var ?string $noOrganizationsMessage */
@endphp

@if($organizations->count() === 0)
    @isset($noOrganizationsMessage)
        <x-bs::alert variant="danger">{{ $noOrganizationsMessage }}</x-bs::alert>
    @endisset
@else
    <div class="list-group">
        @foreach($organizations as $organization)
            @can('view', $organization)
                <x-bs::list.item>
                    <div><a href="{{ route('organizations.show', $organization) }}" class="fw-bold">{{ $organization->name }}</a></div>
                    <div>
                        <i class="fa fa-fw fa-list-check" title="{{ __('Responsibilities') }}"></i>
                        @include('users.shared.responsible_user_span', [
                            'class' => null,
                            'users' => $organization->responsibleUsers,
                        ])
                    </div>
                    <div>
                        <i class="fa fa-fw fa-location-pin" title="{{ __('Location') }}"></i>
                        {{ $organization->location->nameOrAddress }}
                    </div>
                    @isset($organization->website_url)
                        <div>
                            <i class="fa fa-fw fa-display"></i>
                            <a href="{{ $organization->website_url }}" target="_blank">{{ __('Website') }}</a>
                        </div>
                    @endisset
                    @can('update', $organization)
                        <x-bs::button.group class="mt-3">
                            <x-button.edit href="{{ route('organizations.edit', $organization) }}" class="text-nowrap"/>
                        </x-bs::button.group>
                    @endcan
                </x-bs::list.item>
            @endcan
        @endforeach
    </div>
@endif
