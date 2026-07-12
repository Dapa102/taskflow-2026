<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Late Tasks Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 30px; }
        .header { display: flex; align-items: center; gap: 12px; border-bottom: 2px solid #ef4444; padding-bottom: 16px; margin-bottom: 24px; }
        .header img { width: 36px; height: 36px; }
        .header h1 { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
        .header .sub { font-size: 10px; color: #64748b; margin: 0; }
        .meta { display: flex; justify-content: space-between; font-size: 10px; color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #ef4444; color: #fff; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: 20px; left: 30px; right: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .badge-red { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/TaskflowLogo.svg') }}" alt="TaskFlow">
        <div>
            <h1>Late Tasks Report</h1>
            <p class="sub">TaskFlow — Platform Kolaborasi Tugas Antar Tim</p>
        </div>
    </div>

    <div class="meta">
        <span>Generated: {{ now()->format('d F Y H:i') }}</span>
        <span>{{ count($tasks) }} Task(s) Terlambat</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Workspace</th>
                <th>PM</th>
                <th>Member</th>
                <th class="text-center">Deadline</th>
                <th class="text-center">Keterlambatan</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td><strong>{{ $task->title }}</strong></td>
                <td>{{ $task->workspace?->name ?? '-' }}</td>
                <td>{{ $task->assignedPm?->name ?? '-' }}</td>
                <td>{{ $task->assignedMember?->name ?? '-' }}</td>
                <td class="text-center">{{ $task->deadline->format('d M Y') }}</td>
                <td class="text-center"><span class="badge badge-red">{{ $task->deadline->diffInDays(now()) }} hari</span></td>
                <td class="text-center">{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        TaskFlow &copy; {{ date('Y') }} — Confidential
    </div>
</body>
</html>
