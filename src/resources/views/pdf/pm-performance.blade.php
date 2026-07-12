<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PM Performance Report</title>
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
    <h1>PM Performance Report</h1>
    <p>Generated: {{ now()->format('d M Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>PM</th>
                <th>Workspace</th>
                <th>Total Tasks</th>
                <th>Done</th>
                <th>Overdue</th>
                <th>Completion Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pms as $pm)
            <tr>
                <td>{{ $pm->name }} ({{ $pm->email }})</td>
                <td>{{ $pm->workspace->name ?? '-' }}</td>
                <td class="text-center">{{ $pm->total_tasks }}</td>
                <td class="text-center">{{ $pm->done_tasks }}</td>
                <td class="text-center">{{ $pm->overdue_tasks }}</td>
                <td class="text-center">{{ $pm->on_time_rate }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
