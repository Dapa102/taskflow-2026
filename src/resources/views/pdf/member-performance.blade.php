<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Performance Report</title>
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
    <h1>Member Performance Report</h1>
    <p>Generated: {{ now()->format('d M Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Total Tasks</th>
                <th>In Progress</th>
                <th>Review</th>
                <th>Done</th>
                <th>Overdue</th>
                <th>Completion Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
            <tr>
                <td>{{ $member->name }} ({{ $member->email }})</td>
                <td class="text-center">{{ $member->total_tasks }}</td>
                <td class="text-center">{{ $member->in_progress }}</td>
                <td class="text-center">{{ $member->review_tasks }}</td>
                <td class="text-center">{{ $member->done_tasks }}</td>
                <td class="text-center">{{ $member->overdue_tasks }}</td>
                <td class="text-center">{{ $member->completion_rate }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
