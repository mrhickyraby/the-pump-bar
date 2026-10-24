<x-guest-layout>
    <div class="pump-auth__heading">
        <p class="pump-auth__eyebrow">TEAM SIGN IN</p>
        <h1>Welcome back</h1>
        <p>Sign in to access your Pump Bar account.</p>
    </div>

    <x-auth-session-status class="pump-auth__status mb-4" :status="session('status')" />

    <form class="pump-auth__form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="pump-auth__field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pump-auth__field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pump-auth__options">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="pump-auth__submit">
            <span>{{ __('Log in') }}</span>
        </x-primary-button>
    </form>
</x-guest-layout>
