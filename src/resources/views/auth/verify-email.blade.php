<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-xl font-semibold text-gray-900">Verifikasi Email</h2>
        <p class="mt-2 text-sm text-gray-500">{{ __('Terima kasih telah mendaftar! Silakan verifikasi email Anda sebelum memulai.') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-xl">
            <p class="text-sm text-green-700 font-medium">{{ __('Tautan verifikasi baru telah dikirim ke email Anda.') }}</p>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center bg-gray-900 hover:bg-gray-800 focus:ring-gray-400 py-2.5 rounded-xl text-xs">
                {{ __('Kirim Ulang Email Verifikasi') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 hover:text-gray-800 transition-colors">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
