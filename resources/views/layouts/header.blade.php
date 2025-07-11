<header class="d-print-none">
    <nav class="navbar navbar-expand-xl navbar-light bg-light">
        <div class="container-fluid mx-xl-5">
            <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
                    aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHeader">
                {{-- Left Side Of Navbar --}}
                <ul class="navbar-nav me-md-auto">
                    <x-bs::nav.item href="{{ route('dashboard') }}" class="text-nowrap">
                        <i class="fa fa-fw fa-home"></i> {{ __('Dashboard') }}
                    </x-bs::nav.item>

                    @php
                        /** @var ?\App\Models\User $loggedInUser */
                        $loggedInUser = \Illuminate\Support\Facades\Auth::user();

                        $canViewEvents = $loggedInUser?->can('viewAny', App\Models\Event::class);
                        $canViewEventSeries = $loggedInUser?->can('viewAny', App\Models\EventSeries::class);
                    @endphp
                    @if($canViewEventSeries)
                        <x-bs::nav.item id="navbarEventsDropdown">
                            <i class="{{ \App\Enums\AbilityGroup::Events->getIcon() }}"></i> {{ __('Events') }}
                            <x-slot:dropdown>
                                @if($canViewEvents)
                                    <x-bs::dropdown.item href="{{ route('events.index') }}">
                                        <i class="{{ \App\Enums\AbilityGroup::Events->getIcon() }}"></i> {{ __('Events') }}
                                    </x-bs::dropdown.item>
                                @endif
                                @if($canViewEventSeries)
                                    <x-bs::dropdown.item href="{{ route('event-series.index') }}">
                                        <i class="{{ \App\Enums\AbilityGroup::EventSeries->getIcon() }}"></i> {{ __('Event series') }}
                                    </x-bs::dropdown.item>
                                @endif
                            </x-slot:dropdown>
                        </x-bs::nav.item>
                    @elseif($canViewEvents)
                        <x-bs::nav.item href="{{ route('events.index') }}" class="text-nowrap">
                            <i class="{{ \App\Enums\AbilityGroup::Events->getIcon() }}"></i> {{ __('Events') }}
                        </x-bs::nav.item>
                    @endif

                    @php
                        $canViewDocuments = $loggedInUser?->can('viewAny', \App\Models\Document::class);
                    @endphp
                    @if($canViewDocuments)
                        <x-bs::nav.item href="{{ route('documents.index') }}" class="text-nowrap">
                            <i class="{{ \App\Enums\AbilityGroup::Documents->getIcon() }}"></i> {{ __('Documents') }}
                        </x-bs::nav.item>
                    @endif

                    @php
                        $canViewMaterials = $loggedInUser?->can('viewAny', App\Models\Material::class);
                        $canViewStorageLocations = $loggedInUser?->can('viewAny', App\Models\StorageLocation::class);
                    @endphp
                    @if($canViewStorageLocations)
                        <x-bs::nav.item id="navbarMaterialDropdown">
                            <i class="{{ \App\Enums\AbilityGroup::Materials->getIcon() }}"></i> {{ __('Materials') }}
                            <x-slot:dropdown>
                                @if($canViewMaterials)
                                    <x-bs::dropdown.item href="{{ route('materials.search') }}">
                                        <i class="fa fa-fw fa-search"></i> {{ __('Material search') }}
                                    </x-bs::dropdown.item>
                                    <x-bs::dropdown.item href="{{ route('materials.index') }}">
                                        <i class="{{ \App\Enums\AbilityGroup::Materials->getIcon() }}"></i> {{ __('Materials') }}
                                    </x-bs::dropdown.item>
                                @endif
                                @if($canViewStorageLocations)
                                    <x-bs::dropdown.item href="{{ route('storage-locations.index') }}">
                                        <i class="{{ \App\Enums\AbilityGroup::StorageLocations->getIcon() }}"></i> {{ __('Storage locations') }}
                                    </x-bs::dropdown.item>
                                @endif
                            </x-slot:dropdown>
                        </x-bs::nav.item>
                    @elseif($canViewMaterials)
                        <x-bs::nav.item href="{{ route('materials.index') }}" class="text-nowrap">
                            <i class="{{ \App\Enums\AbilityGroup::Materials->getIcon() }}"></i> {{ __('Materials') }}
                        </x-bs::nav.item>
                    @endif
                </ul>

                {{-- Right Side Of Navbar --}}
                <ul class="navbar-nav ms-md-auto mt-0">
                    @guest
                        <x-bs::nav.item href="{{ route('login') }}">
                            <i class="fa fa-fw fa-sign-in-alt"></i>
                            {{ __('Login') }}
                        </x-bs::nav.item>
                    @elseauth
                        @php
                            $canViewOrganizations = $loggedInUser?->can('viewAny', App\Models\Organization::class);
                            $canViewLocations = $loggedInUser?->can('viewAny', App\Models\Location::class);

                            $canViewUsers = $loggedInUser->can('viewAny', App\Models\User::class);
                            $canViewUserRoles = $loggedInUser->can('viewAny', App\Models\UserRole::class);
                            $canViewSystemInformation = $loggedInUser->can('viewSystemInformation', \App\Models\User::class);

                            $canAdmin = $canViewEvents || $canViewEventSeries
                                || $canViewOrganizations || $canViewLocations
                                || $canViewUsers || $canViewUserRoles || $canViewSystemInformation;
                        @endphp
                        @if($canAdmin)
                            <x-bs::nav.item id="navbarAdminDropdown">
                                <i class="fa fa-wrench"></i>
                                {{ __('Administration') }}
                                <x-slot:dropdown>
                                    @if($canViewOrganizations)
                                        <x-bs::dropdown.item href="{{ route('organizations.index') }}">
                                            <i class="{{ \App\Enums\AbilityGroup::Organizations->getIcon() }}"></i> {{ __('Organizations') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewLocations)
                                        <x-bs::dropdown.item href="{{ route('locations.index') }}">
                                            <i class="{{ \App\Enums\AbilityGroup::Locations->getIcon() }}"></i> {{ __('Locations') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewUsers || $canViewUserRoles)
                                        <li class="dropdown-divider"></li>
                                    @endif
                                    @if($canViewUsers)
                                        <x-bs::dropdown.item href="{{ route('users.index') }}">
                                            <i class="{{ \App\Enums\AbilityGroup::Users->getIcon() }}"></i> {{ __('Users') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewUserRoles)
                                        <x-bs::dropdown.item href="{{ route('user-roles.index') }}">
                                            <i class="{{ \App\Enums\AbilityGroup::UserRoles->getIcon() }}"></i> {{ __('User roles') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewSystemInformation)
                                        <li class="dropdown-divider"></li>
                                        <x-bs::dropdown.item href="{{ route('system-info.index') }}">
                                            <i class="fa fa-fw fa-cog"></i> {{ __('System information') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                </x-slot:dropdown>
                            </x-bs::nav.item>
                        @endif
                        <x-bs::nav.item id="navbarUserDropdown">
                            <i class="fa fa-fw fa-user-circle"></i> {{ $loggedInUser->first_name }}
                            <x-slot:dropdown class="dropdown-menu-end">
                                @can('viewAccount', \App\Models\User::class)
                                    <x-bs::dropdown.item href="{{ route('account.show') }}">
                                        <i class="fa fa-fw fa-user-cog"></i> {{ __('My account') }}
                                    </x-bs::dropdown.item>
                                @elsecan('editAccount', \App\Models\User::class)
                                    <x-bs::dropdown.item href="{{ route('account.edit') }}">
                                        <i class="fa fa-fw fa-user-pen"></i> {{ __('Edit my account') }}
                                    </x-bs::dropdown.item>
                                @endcan
                                @can('viewOwn', \App\Models\PersonalAccessToken::class)
                                    <x-bs::dropdown.item href="{{ route('personal-access-tokens.index') }}">
                                        <i class="fa fa-fw fa-id-card-clip"></i> {{ __('Personal access tokens') }}
                                    </x-bs::dropdown.item>
                                @endcan
                                @can('viewDocumentation', \App\Models\PersonalAccessToken::class)
                                    <x-bs::dropdown.item href="{{ route('api-docs.index') }}">
                                        <i class="fa fa-fw fa-file-code"></i> {{ __('API documentation') }}
                                    </x-bs::dropdown.item>
                                @endcan
                                <li>
                                    <x-bs::form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        <button type="submit" class="dropdown-item"><i class="fa fa-fw fa-sign-out-alt"></i> {{ __('Logout') }}</button>
                                    </x-bs::form>
                                </li>
                            </x-slot:dropdown>
                        </x-bs::nav.item>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
