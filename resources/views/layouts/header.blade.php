<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-2">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                {{ config('app.name') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
                    aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHeader">
                @auth
                    {{-- Left Side Of Navbar --}}
                    <ul class="navbar-nav me-md-auto">
                        <x-nav.item href="{{ route('dashboard') }}">
                            <i class="fa fa-home"></i>
                            {{ __('Dashboard') }}
                        </x-nav.item>
                    </ul>
                @endauth

                {{-- Right Side Of Navbar --}}
                <ul class="navbar-nav ms-md-auto mt-0">
                    @guest
                        <x-nav.item href="{{ route('login') }}">
                            <i class="fa fa-sign-in-alt"></i>
                            {{ __('Login') }}
                        </x-nav.item>
                    @elseauth
                        @php
                            /** @var \App\Models\User $loggedInUser */
                            $loggedInUser = \Illuminate\Support\Facades\Auth::user();

                            $canViewUsers = $loggedInUser->can('viewAny', App\Models\User::class);
                            $canViewUserRoles = $loggedInUser->can('viewAny', App\Models\UserRole::class);
                            $canAdmin = $canViewUsers || $canViewUserRoles;
                        @endphp
                        @if($canAdmin)
                            <li class="nav-item dropdown">
                                <a id="navbarAdminDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-wrench"></i>
                                    {{ __('Administration') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarAdminDropdown">
                                    @if($canViewUsers)
                                        <x-nav.dropdown-item href="{{ route('users.index') }}">
                                            <i class="fa fa-fw fa-users"></i>
                                            {{ __('Users') }}
                                        </x-nav.dropdown-item>
                                    @endif
                                    @if($canViewUserRoles)
                                        <x-nav.dropdown-item href="{{ route('user-roles.index') }}">
                                            <i class="fa fa-fw fa-user-group"></i>
                                            {{ __('User roles') }}
                                        </x-nav.dropdown-item>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a id="navbarUserDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle"></i>
                                {{ $loggedInUser->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                @if($loggedInUser->can('editAccount', \App\Models\User::class))
                                    <x-nav.dropdown-item href="{{ route('account.edit') }}">
                                        <i class="fa fa-fw fa-user-cog"></i>
                                        {{ __('My account') }}
                                    </x-nav.dropdown-item>
                                @endif
                                @if($loggedInUser->can('viewOwn', \App\Models\PersonalAccessToken::class))
                                    <x-nav.dropdown-item href="{{ route('personal-access-tokens.index') }}">
                                        <i class="fa fa-fw fa-id-card-clip"></i>
                                        {{ __('Personal access tokens') }}
                                    </x-nav.dropdown-item>
                                @endif
                                <x-nav.dropdown-item href="{{ route('logout') }}"
                                                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-fw fa-sign-out-alt"></i>
                                    {{ __('Logout') }}
                                </x-nav.dropdown-item>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                </form>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
