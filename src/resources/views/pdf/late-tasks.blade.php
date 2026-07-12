<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Late Tasks Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        h1 { text-align: center; color: #333; }
    </style>
</head>
<body>
    <h1>Late Tasks Report</h1>
    <p>Generated: {{ now()->format('d M Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Workspace</th>
                <th>PM</th>
                <th>Member</th>
                <th>Deadline</th>
                <th>Days Late</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>{{ $task->workspace?->name ?? '-' }}</td>
                <td>{{ $task->assignedPm?->name ?? '-' }}</td>
                <td>{{ $task->assignedMember?->name ?? '-' }}</td>
                <td class="text-center">{{ $task->deadline->format('d M Y') }}</td>
                <td class="text-center">{{ $task->deadline->diffInDays(now()) }} hari</td>
                <td class="text-center">{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
