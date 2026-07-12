<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Verify Email</h2>
        <p class="mt-1.5 text-sm text-gray-500">{{ __('Thanks for signing up! Please verify your email address before getting started.') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3 bg-green-50 border border-green-100 rounded-lg">
            <p class="text-sm text-green-700 font-medium">{{ __('A new verification link has been sent to the email address you provided.') }}</p>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center bg-gray-900 hover:bg-gray-800 focus:ring-gray-400">
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
