<div>
    <x-slot name="header">
        <div class="flex space-x-4 items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('PM Performance Metrics') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:underline">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="mb-4 text-sm text-gray-500">
                    <i class="fa fa-info-circle"></i> Data is cached for 5 minutes.
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Manager</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workspace</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tasks</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Done</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pms as $pm)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $pm->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $pm->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pm->workspace->name ?? 'No Workspace' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    {{ $pm->total_tasks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-green-600">
                                    {{ $pm->done_tasks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-red-600">
                                    {{ $pm->overdue_tasks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="text-sm font-medium mr-2 {{ $pm->on_time_rate > 70 ? 'text-green-600' : ($pm->on_time_rate < 30 && $pm->total_tasks > 0 ? 'text-red-600' : 'text-yellow-600') }}">
                                            {{ $pm->on_time_rate }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No Project Managers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
