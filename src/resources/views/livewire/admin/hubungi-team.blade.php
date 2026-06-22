<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hubungi Team
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('message'))
                    <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit="send" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih Project Manager</label>
                        <select wire:model.live="recipientId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih PM</option>
                            @foreach ($recipients as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }}{{ $pm->phone ? ' - ' . $pm->phone : '' }})</option>
                            @endforeach
                        </select>
                        @error('recipientId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    @if($recipientId && $pmTeams->isNotEmpty())
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-indigo-800 mb-3">Tim yang dipimpin:</h4>
                        <div class="space-y-2">
                            @foreach($pmTeams as $team)
                            <div class="bg-white rounded-md p-3 border border-indigo-100">
                                <div class="text-sm font-medium text-gray-900 mb-1">{{ $team->name }}</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($team->members as $member)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $member->user?->name ?? '?' }}
                                        <span class="{{ $member->role === 'admin' ? 'text-purple-500' : 'text-gray-400' }}">({{ $member->role === 'admin' ? 'Project Manager' : 'Anggota' }})</span>
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @elseif($recipientId && $pmTeams->isEmpty())
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">PM ini belum memiliki tim.</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Kirim</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 px-4 py-2 border rounded-md cursor-pointer {{ $sendType === 'email' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                <input type="radio" wire:model="sendType" value="email" class="text-blue-600">
                                <span class="text-sm font-medium">Email</span>
                            </label>
                            <label class="flex items-center gap-2 px-4 py-2 border rounded-md cursor-pointer {{ $sendType === 'whatsapp' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                                <input type="radio" wire:model="sendType" value="whatsapp" class="text-green-600">
                                <span class="text-sm font-medium">WhatsApp</span>
                            </label>
                        </div>
                    </div>

                    @if($sendType === 'email')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subjek</label>
                        <input type="text" wire:model="subject"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pesan</label>
                        <textarea wire:model="body" rows="6"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 {{ $sendType === 'whatsapp' ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-md">
                            {{ $sendType === 'whatsapp' ? 'Kirim WhatsApp' : 'Kirim Email' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
