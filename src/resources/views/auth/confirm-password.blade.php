<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-xl font-semibold text-gray-900">Konfirmasi Password</h2>
        <p class="mt-2 text-sm text-gray-500">{{ __('Konfirmasi password Anda untuk melanjutkan.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />
            <x-text-input id="password" class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-300 focus:ring-0 transition-all" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button class="bg-gray-900 hover:bg-gray-800 focus:ring-gray-400 px-5 py-2.5 rounded-xl text-xs">
                {{ __('Konfirmasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
