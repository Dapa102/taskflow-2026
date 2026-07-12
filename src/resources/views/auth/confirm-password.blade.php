<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Confirm Password</h2>
        <p class="mt-1.5 text-sm text-gray-500">{{ __('Please confirm your password before continuing.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="password" class="block mt-1.5 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-400 focus:ring-0 transition-colors" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button class="bg-gray-900 hover:bg-gray-800 focus:ring-gray-400">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
