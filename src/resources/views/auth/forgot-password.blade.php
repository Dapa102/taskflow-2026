<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Reset Password</h2>
        <p class="mt-1.5 text-sm text-gray-500">{{ __('No problem. Enter your email and we will send you a reset link.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-1.5 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:border-gray-400 focus:ring-0 transition-colors" type="email" name="email" :value="old('email')" required autofocus placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                &larr; {{ __('Back to login') }}
            </a>
            <x-primary-button class="bg-gray-900 hover:bg-gray-800 focus:ring-gray-400">
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
