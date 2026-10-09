<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Training schedule</title>
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1f2937; }
    h1 { font-size: 16px; margin: 0; } .muted { color: #6b7280; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th { background: #111110; color: #FFE500; text-align: left; padding: 6px; font-size: 9px; text-transform: uppercase; }
    td { padding: 6px; border-bottom: 1px solid #e5e7eb; }
</style></head>
<body>
    <h1>{{ $settings->site_name ?? config('app.name') }} — Training schedule</h1>
    <p class="muted">{{ $instructor->firstName }} {{ $instructor->lastName }} · generated {{ now()->format('j M Y, g:ia') }}</p>
    <table>
        <thead><tr><th>Date</th><th>Time</th><th>Course</th><th>Topic</th><th>Type</th><th>Batch</th><th>Students</th></tr></thead>
        <tbody>
            @forelse ($schedules as $s)
                <tr>
                    <td>{{ $s->schedule_date->format('D j M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($s->time_start)->format('g:ia') }}–{{ \Carbon\Carbon::parse($s->time_stop)->format('g:ia') }}</td>
                    <td>{{ $s->course?->objective }}</td>
                    <td>{{ $s->topic?->topic }}</td>
                    <td>{{ ucfirst($s->session_type ?? 'theory') }}</td>
                    <td>{{ $s->batch?->batch_name }}</td>
                    <td>{{ count($s->studentIds()) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No sessions in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body></html>
