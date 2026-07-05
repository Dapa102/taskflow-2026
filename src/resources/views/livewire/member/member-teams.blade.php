<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tim Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($myTeams->isNotEmpty())
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tim</h3>
                <div class="space-y-4">
                    @foreach($myTeams as $tm)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $tm->team->name }}</p>
                            @if($tm->team->owner)
                            <p class="text-sm text-gray-500 mt-1">
                                Project Manager: <span class="font-medium text-indigo-600">{{ $tm->team->owner->name }}</span>
                                @if($tm->team->owner->email) &middot; {{ $tm->team->owner->email }} @endif
                            </p>
                            @endif
                        </div>
                        @if($tm->team->owner?->phone)
                        <a href="https://wa.me/{{ $tm->team->owner->phone }}" target="_blank"
                           class="px-3 py-1.5 text-xs font-medium bg-green-50 text-green-700 rounded-md hover:bg-green-100">
                            WhatsApp
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($memberWorkspaces->isNotEmpty())
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Workspace</h3>
                <div class="space-y-4">
                    @foreach($memberWorkspaces as $ws)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $ws->name }}</p>
                            @if($ws->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $ws->description }}</p>
                            @endif
                            @if($ws->pm)
                            <p class="text-sm text-gray-500 mt-1">
                                PM: <span class="font-medium text-indigo-600">{{ $ws->pm->name }}</span>
                                @if($ws->pm->email) &middot; {{ $ws->pm->email }} @endif
                            </p>
                            @endif
                        </div>
                        @if($ws->pm?->phone)
                        <a href="https://wa.me/{{ $ws->pm->phone }}" target="_blank"
                           class="px-3 py-1.5 text-xs font-medium bg-green-50 text-green-700 rounded-md hover:bg-green-100">
                            WhatsApp
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($myTeams->isEmpty() && $memberWorkspaces->isEmpty())
            <div class="text-center text-gray-400 text-sm py-16">Belum tergabung dalam tim atau workspace mana pun.</div>
            @endif
        </div>
    </div>
</div>
