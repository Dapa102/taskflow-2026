<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-xl font-semibold text-gray-900">Reset Password</h2>
        <p class="mt-2 text-sm text-gray-500">{{ __('Masukkan email Anda dan kami akan kirim tautan reset.') }}</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-gray-700" />
            <x-text-input id="email" class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-300 focus:ring-0 transition-all" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@perusahaan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                &larr; {{ __('Kembali') }}
            </a>
            <x-primary-button class="bg-gray-900 hover:bg-gray-800 focus:ring-gray-400 px-5 py-2.5 rounded-xl text-xs">
                {{ __('Kirim Tautan Reset') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
