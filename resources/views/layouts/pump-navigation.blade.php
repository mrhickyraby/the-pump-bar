<header class="pump-header" x-data="{ menuOpen: false }">
    <div class="pump-header__top">
        <a class="pump-brand {{ file_exists(public_path('images/the-pump-bar-logo.svg')) ? 'pump-brand--with-logo' : '' }}" href="{{ route('dashboard') }}" aria-label="The Pump Bar dashboard">
            @if(file_exists(public_path('images/the-pump-bar-logo.svg')))
                <img class="pump-brand__logo" src="{{ asset('images/the-pump-bar-logo.svg') }}" alt="The Pump Bar logo">
            @else
                <span class="pump-brand__mark">PB</span>
                <span class="pump-brand__copy">
                    <span class="pump-brand__name">The Pump Bar</span>
                    <span class="pump-brand__tagline">Branch operations</span>
                </span>
            @endif
        </a>
        <button class="pump-menu-toggle" type="button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen.toString()" aria-controls="pump-main-navigation" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
        <nav id="pump-main-navigation" class="pump-nav" :class="{ 'is-open': menuOpen }" aria-label="Main navigation">
            <div class="pump-nav__links">
                @if(! Auth::user()->isStaff())
                    <a class="pump-nav__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
                @endif
                @if(Auth::user()->isOwner() || Auth::user()->isManager())
                    <a class="pump-nav__link {{ request()->routeIs('branches.*') ? 'is-active' : '' }}" href="{{ route('branches.index') }}" @if(request()->routeIs('branches.*')) aria-current="page" @endif>Branches</a>
                @endif
                <a class="pump-nav__link {{ request()->routeIs('products.*') ? 'is-active' : '' }}" href="{{ route('products.index') }}" @if(request()->routeIs('products.*')) aria-current="page" @endif>Products</a>
                @if(Auth::user()->isOwner())
                    <a class="pump-nav__link {{ request()->routeIs('ingredients.*') ? 'is-active' : '' }}" href="{{ route('ingredients.index') }}" @if(request()->routeIs('ingredients.*')) aria-current="page" @endif>Ingredients</a>
                @endif
                @if(Auth::user()->isOwner() || Auth::user()->isManager())
                    <a class="pump-nav__link {{ request()->routeIs('inventory.*') ? 'is-active' : '' }}" href="{{ route('inventory.index') }}" @if(request()->routeIs('inventory.*')) aria-current="page" @endif>Inventory</a>
                @endif
                <a class="pump-nav__link {{ request()->routeIs('pos.*') ? 'is-active' : '' }}" href="{{ route('pos.index') }}" @if(request()->routeIs('pos.*')) aria-current="page" @endif>POS</a>
                @if(Auth::user()->isOwner() || Auth::user()->isManager())
                    <a class="pump-nav__link {{ request()->routeIs('expenses.*') ? 'is-active' : '' }}" href="{{ route('expenses.index') }}" @if(request()->routeIs('expenses.*')) aria-current="page" @endif>Expenses</a>
                    <a class="pump-nav__link {{ request()->routeIs('reports.*') ? 'is-active' : '' }}" href="{{ route('reports.index') }}" @if(request()->routeIs('reports.*')) aria-current="page" @endif>Reports</a>
                @endif
                @if(Auth::user()->isOwner())
                    <a class="pump-nav__link {{ request()->routeIs('users.*') ? 'is-active' : '' }}" href="{{ route('users.index') }}" @if(request()->routeIs('users.*')) aria-current="page" @endif>Users</a>
                @endif
            </div>
            <div class="pump-nav__account">
                <span class="pump-account__role">{{ ucfirst(Auth::user()->role) }}</span>
                <form class="pump-nav__logout" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log out</button>
                </form>
            </div>
        </nav>
    </div>
</header>