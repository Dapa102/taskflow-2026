<div class="space-y-4">
    @if($task->description)
        <div>
            <h4 class="text-sm font-medium text-gray-500">Deskripsi</h4>
            <p class="mt-1 text-sm">{{ $task->description }}</p>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-500">Status</h4>
            <p class="mt-1">
                <x-filament::badge :color="match($task->status) { 'todo' => 'info', 'on_progress' => 'warning', 'done' => 'success', default => 'gray' }">
                    {{ match($task->status) { 'todo' => 'To-Do', 'on_progress' => 'On Progress', 'done' => 'Done', default => $task->status } }}
                </x-filament::badge>
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Prioritas</h4>
            <p class="mt-1">
                <x-filament::badge :color="match($task->priority) { 'low' => 'gray', 'medium' => 'warning', 'high' => 'danger', default => 'gray' }">
                    {{ match($task->priority) { 'low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', default => $task->priority } }}
                </x-filament::badge>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-500">Kategori</h4>
            <p class="mt-1 text-sm">{{ $task->category?->name ?? '—' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Tim</h4>
            <p class="mt-1 text-sm">{{ $task->team?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-500">Tenggat</h4>
            <p class="mt-1 text-sm">{{ $task->deadline?->format('d M Y') ?? 'Tidak ada' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500">Progress Subtugas</h4>
            <p class="mt-1 text-sm">
                @php $total = $task->subtasks()->count(); $done = $task->subtasks()->where('is_completed', true)->count(); @endphp
                {{ $total > 0 ? "{$done}/{$total}" : '—' }}
            </p>
        </div>
    </div>

    @php
        $now = now();
        $deadline = $task->deadline;
    @endphp

    @if($deadline)
        @if($task->status === 'done')
            <div class="p-3 rounded-lg bg-success-50 border border-success-200">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 text-success-600" />
                    <span class="text-sm font-medium text-success-700">Tugas selesai</span>
                </div>
            </div>
        @elseif($deadline->isPast())
            @php $diff = $now->diff($deadline); @endphp
            <div class="p-3 rounded-lg bg-danger-50 border border-danger-200">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5 text-danger-600" />
                    <span class="text-sm font-medium text-danger-700">
                        Terlambat {{ $diff->d > 0 ? $diff->d . ' hari ' : '' }}{{ $diff->h > 0 ? $diff->h . ' jam' : '' }} lalu
                    </span>
                </div>
            </div>
        @elseif($deadline->diffInHours($now) <= 24)
            @php $diff = $now->diff($deadline); @endphp
            <div class="p-3 rounded-lg bg-warning-50 border border-warning-200">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-clock" class="w-5 h-5 text-warning-600" />
                    <span class="text-sm font-medium text-warning-700">
                        Tenggat dalam {{ $diff->h > 0 ? $diff->h . ' jam ' : '' }}{{ $diff->m > 0 ? $diff->m . ' menit' : '' }} lagi
                    </span>
                </div>
            </div>
        @else
            @php $diff = $now->diff($deadline); @endphp
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-calendar" class="w-5 h-5 text-gray-600" />
                    <span class="text-sm font-medium text-gray-700">
                        Sisa {{ $diff->d > 0 ? $diff->d . ' hari' : '' }}{{ $diff->h > 0 ? ' ' . $diff->h . ' jam' : '' }}
                    </span>
                </div>
            </div>
        @endif
    @endif

    @if($task->assignees->isNotEmpty())
        <div>
            <h4 class="text-sm font-medium text-gray-500">Ditugaskan ke</h4>
            <div class="mt-1 flex flex-wrap gap-2">
                @foreach($task->assignees as $user)
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100">
                        <span class="w-4 h-4 rounded-full bg-gray-400 flex items-center justify-center text-[10px] text-white font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        {{ $user->name }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    @if($task->comments->isNotEmpty())
        <div>
            <h4 class="text-sm font-medium text-gray-500">Komentar ({{ $task->comments->count() }})</h4>
            <div class="mt-1 space-y-2">
                @foreach($task->comments->take(3) as $comment)
                    <div class="p-2 rounded bg-gray-50 text-sm">
                        <span class="font-medium">{{ $comment->user->name }}</span>: {{ $comment->content }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($task->attachments->isNotEmpty())
        <div>
            <h4 class="text-sm font-medium text-gray-500">Lampiran ({{ $task->attachments->count() }})</h4>
            <div class="mt-1 space-y-1">
                @foreach($task->attachments as $att)
                    <div class="text-sm flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-paper-clip" class="w-4 h-4 text-gray-400" />
                        {{ $att->filename }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
