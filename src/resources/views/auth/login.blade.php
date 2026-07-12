<x-guest-layout>
    <div class="text-center mb-10">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
        <p class="mt-2 text-sm text-gray-500">Silakan masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl">
            <p class="text-sm text-red-600">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-gray-700" />
            <x-text-input id="email" class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-300 focus:ring-0 transition-all" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />
                @if (Route::has('password.request'))
                    <a class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-300 focus:ring-0 transition-all"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-400 focus:ring-offset-0 cursor-pointer" name="remember">
                <span class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full px-4 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                {{ __('Masuk') }}
            </button>
        </div>
    </form>
</x-guest-layout>
