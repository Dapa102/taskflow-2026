<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Late Tasks Report</title>
    <style>
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path('fonts/Inter-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 700;
            src: url('{{ storage_path('fonts/Inter-Bold.ttf') }}') format('truetype');
        }
        body {
            font-family: 'Inter', 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header img {
            width: 36px;
            height: 36px;
            margin-bottom: 4px;
        }
        .header .app-name {
            font-size: 11px;
            font-weight: 700;
            color: #2563eb;
            margin: 0;
        }
        .header .sub {
            font-size: 9px;
            color: #64748b;
            margin: 2px 0 0;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #64748b;
            margin-bottom: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        td {
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: 700; }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/TaskflowLogo.svg') }}" alt="TaskFlow" width="36" height="36">
        <p class="app-name">TaskFlow</p>
        <p class="sub">Platform Kolaborasi Tugas Antar Tim</p>
    </div>

    <div class="report-title">Late Tasks Report</div>

    <div class="meta">
        <span>Tanggal: {{ now()->format('d F Y H:i') }}</span>
        <span>{{ count($tasks) }} Tugas Terlambat</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Workspace</th>
                <th>PM</th>
                <th>Member</th>
                <th class="text-center">Deadline</th>
                <th class="text-right">Keterlambatan</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td><span class="fw-bold">{{ $task->title }}</span></td>
                <td>{{ $task->workspace?->name ?? '-' }}</td>
                <td>{{ $task->assignedPm?->name ?? '-' }}</td>
                <td>{{ $task->assignedMember?->name ?? '-' }}</td>
                <td class="text-center">{{ $task->deadline->format('d M Y') }}</td>
                <td class="text-right">{{ $task->deadline->diffInDays(now()) }} hari</td>
                <td class="text-center">{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        TaskFlow &copy; {{ date('Y') }} — Laporan ini bersifat rahasia
    </div>
</body>
</html>
