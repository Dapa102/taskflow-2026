<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            @if(session('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('message') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="flex justify-end">
                <button wire:click="$toggle('showCreateForm')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                    {{ $showCreateForm ? 'Batal' : '+ Tambah User' }}
                </button>
            </div>

            @if($showCreateForm)
            <div class="p-6 bg-white shadow sm:rounded-lg border border-indigo-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah User Baru</h3>
                <form wire:submit="createUser" class="grid grid-cols-2 gap-4 max-w-2xl">
                    <div>
                        <x-input-label value="Nama" />
                        <x-text-input wire:model="name" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Email" />
                        <x-text-input wire:model="email" type="email" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Password" />
                        <x-text-input wire:model="password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Role" />
                        <select wire:model="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="member">Anggota</option>
                            <option value="pm">Project Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="No. Telepon (opsional)" />
                        <x-text-input wire:model="phone" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>
                    <div class="flex items-end">
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
            @endif

            <div class="p-4 bg-white shadow sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleLabel = match($user->role) {
                                            'admin' => 'Admin',
                                            'super_admin' => 'Super Admin',
                                            'pm' => 'Project Manager',
                                            'member' => 'Anggota',
                                            default => ucfirst($user->role),
                                        };
                                        $roleClass = match($user->role) {
                                            'admin' => 'bg-red-100 text-red-800',
                                            'super_admin' => 'bg-purple-100 text-purple-800',
                                            'pm' => 'bg-indigo-100 text-indigo-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Suspended</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(in_array($user->role, ['admin', 'super_admin']))
                                        <span class="text-gray-400 text-xs">—</span>
                                    @else
                                    <button wire:click="toggleUserStatus({{ $user->id }})" wire:confirm="Toggle status for this user?" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                    </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button x-data @click="$dispatch('open-modal', 'contact-user-{{ $user->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        Hubungi
                                    </button>

                                    <x-modal name="contact-user-{{ $user->id }}" maxWidth="md">
                                        <div class="p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-semibold text-gray-900">Hubungi {{ $user->name }}</h3>
                                                <button @click="$dispatch('close-modal', 'contact-user-{{ $user->id }}')" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-4">
                                                {{ $user->email }}
                                                @if($user->phone) &middot; {{ $user->phone }} @endif
                                            </p>
                                            <div class="space-y-3">
                                                <a href="{{ route('super-admin.hubungi-team') }}?recipient={{ $user->id }}"
                                                   class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                                    Kirim Email
                                                </a>
                                                <a href="{{ route('super-admin.hubungi-team') }}?recipient={{ $user->id }}&sendType=whatsapp"
                                                   class="flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    Kirim WhatsApp
                                                </a>
                                            </div>
                                        </div>
                                    </x-modal>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
