<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-2">
            <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
                    aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHeader">
                {{-- Left Side Of Navbar --}}
                <ul class="navbar-nav me-md-auto">
                    <x-bs::nav.item href="{{ route('dashboard') }}">
                        <i class="fa fa-fw fa-home"></i>
                        {{ __('Dashboard') }}
                    </x-bs::nav.item>
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
                            /** @var \App\Models\User $loggedInUser */
                            $loggedInUser = \Illuminate\Support\Facades\Auth::user();

                            $canViewEvents = $loggedInUser->can('viewAny', App\Models\Event::class);
                            $canViewEventSeries = $loggedInUser->can('viewAny', App\Models\EventSeries::class);
                            $canViewOrganizations = $loggedInUser->can('viewAny', App\Models\Organization::class);
                            $canViewLocations = $loggedInUser->can('viewAny', App\Models\Location::class);
                            $canViewDocuments = $loggedInUser->can('viewAny', \App\Models\Document::class);

                            $canViewUsers = $loggedInUser->can('viewAny', App\Models\User::class);
                            $canViewUserRoles = $loggedInUser->can('viewAny', App\Models\UserRole::class);

                            $canAdmin = $canViewEvents || $canViewEventSeries
                                || $canViewOrganizations || $canViewLocations
                                || $canViewUsers || $canViewUserRoles;
                        @endphp
                        @if($canAdmin)
                            <x-bs::nav.item id="navbarAdminDropdown">
                                <i class="fa fa-wrench"></i>
                                {{ __('Administration') }}
                                <x-slot:dropdown>
                                    @if($canViewEvents)
                                        <x-bs::dropdown.item href="{{ route('events.index') }}">
                                            <i class="fa fa-fw fa-calendar-days"></i>
                                            {{ __('Events') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewEventSeries)
                                        <x-bs::dropdown.item href="{{ route('event-series.index') }}">
                                            <i class="fa fa-fw fa-calendar-week"></i>
                                            {{ __('Event series') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewOrganizations)
                                        <x-bs::dropdown.item href="{{ route('organizations.index') }}">
                                            <i class="fa fa-fw fa-sitemap"></i>
                                            {{ __('Organizations') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewLocations)
                                        <x-bs::dropdown.item href="{{ route('locations.index') }}">
                                            <i class="fa fa-fw fa-location-pin"></i>
                                            {{ __('Locations') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewDocuments)
                                        <x-bs::dropdown.item href="{{ route('documents.index') }}">
                                            <i class="fa fa-fw fa-file"></i>
                                            {{ __('Documents') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewUsers || $canViewUserRoles)
                                        <li class="dropdown-divider"></li>
                                    @endif
                                    @if($canViewUsers)
                                        <x-bs::dropdown.item href="{{ route('users.index') }}">
                                            <i class="fa fa-fw fa-users"></i>
                                            {{ __('Users') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                    @if($canViewUserRoles)
                                        <x-bs::dropdown.item href="{{ route('user-roles.index') }}">
                                            <i class="fa fa-fw fa-user-group"></i>
                                            {{ __('User roles') }}
                                        </x-bs::dropdown.item>
                                    @endif
                                </x-slot:dropdown>
                            </x-bs::nav.item>
                        @endif
                        <x-bs::nav.item id="navbarUserDropdown">
                            <i class="fa fa-user-circle"></i>
                            {{ $loggedInUser->name }}
                            <x-slot:dropdown class="dropdown-menu-end">
                                @can('viewAccount', \App\Models\User::class)
                                    <x-bs::dropdown.item href="{{ route('account.show') }}">
                                        <i class="fa fa-fw fa-user-cog"></i> {{ __('My account') }}
                                    </x-bs::dropdown.item>
                                @elsecan('editAccount', \App\Models\User::class)
                                    <x-bs::dropdown.item href="{{ route('account.edit') }}">
                                        <i class="fa fa-fw fa-user-cog"></i> {{ __('My account') }}
                                    </x-bs::dropdown.item>
                                @endcan
                                @if($loggedInUser->can('viewOwn', \App\Models\PersonalAccessToken::class))
                                    <x-bs::dropdown.item href="{{ route('personal-access-tokens.index') }}">
                                        <i class="fa fa-fw fa-id-card-clip"></i>
                                        {{ __('Personal access tokens') }}
                                    </x-bs::dropdown.item>
                                @endif
                                <x-bs::dropdown.item href="{{ route('logout') }}"
                                                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-fw fa-sign-out-alt"></i>
                                    {{ __('Logout') }}
                                </x-bs::dropdown.item>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                </form>
                            </x-slot:dropdown>
                        </x-bs::nav.item>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
