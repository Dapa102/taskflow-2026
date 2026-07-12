<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Performance Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 30px; }
        .header { display: flex; align-items: center; gap: 12px; border-bottom: 2px solid #2563eb; padding-bottom: 16px; margin-bottom: 24px; }
        .header img { width: 36px; height: 36px; }
        .header h1 { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
        .header .sub { font-size: 10px; color: #64748b; margin: 0; }
        .meta { display: flex; justify-content: space-between; font-size: 10px; color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2563eb; color: #fff; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: 20px; left: 30px; right: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/TaskflowLogo.svg') }}" alt="TaskFlow">
        <div>
            <h1>Member Performance Report</h1>
            <p class="sub">TaskFlow — Platform Kolaborasi Tugas Antar Tim</p>
        </div>
    </div>

    <div class="meta">
        <span>Generated: {{ now()->format('d F Y H:i') }}</span>
        <span>{{ count($members) }} Member(s)</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th class="text-center">Total</th>
                <th class="text-center">In Progress</th>
                <th class="text-center">Review</th>
                <th class="text-center">Done</th>
                <th class="text-center">Overdue</th>
                <th class="text-center">Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
            <tr>
                <td><strong>{{ $member->name }}</strong><br><span style="font-size:10px;color:#64748b;">{{ $member->email }}</span></td>
                <td class="text-center">{{ $member->total_tasks }}</td>
                <td class="text-center">{{ $member->in_progress }}</td>
                <td class="text-center">{{ $member->review_tasks }}</td>
                <td class="text-center">{{ $member->done_tasks }}</td>
                <td class="text-center">
                    @if($member->overdue_tasks > 0)
                        <span class="badge badge-red">{{ $member->overdue_tasks }}</span>
                    @else
                        {{ $member->overdue_tasks }}
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge {{ $member->completion_rate >= 80 ? 'badge-green' : 'badge-red' }}">{{ $member->completion_rate }}%</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        TaskFlow &copy; {{ date('Y') }} — Confidential
    </div>
</body>
</html>
