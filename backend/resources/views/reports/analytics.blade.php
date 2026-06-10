<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analytics Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #1e1e1e; }
        h1 { font-size: 22px; color: #2d6a4f; border-bottom: 2px solid #2d6a4f; padding-bottom: 6px; }
        h2 { font-size: 16px; color: #2d6a4f; margin-top: 20px; }
        h3 { font-size: 13px; color: #555; margin-bottom: 4px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { border: none; margin-bottom: 2px; }
        .header p { color: #777; font-size: 11px; margin-top: 2px; }
        .stats-grid { display: flex; justify-content: space-between; margin: 16px 0; }
        .stat-box { text-align: center; padding: 10px 16px; border: 1px solid #ddd; border-radius: 6px; flex: 1; margin: 0 4px; }
        .stat-box .value { font-size: 20px; font-weight: bold; color: #2d6a4f; }
        .stat-box .label { font-size: 9px; color: #888; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #2d6a4f; color: #fff; font-size: 10px; text-transform: uppercase; padding: 8px 10px; text-align: left; }
        td { padding: 6px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        .category-bar { height: 14px; background: #d8f3dc; border-radius: 3px; margin: 3px 0; overflow: hidden; }
        .category-bar-fill { height: 100%; background: #2d6a4f; border-radius: 3px; }
        .footer { text-align: center; color: #aaa; font-size: 9px; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
        .section { margin-bottom: 16px; }
        .badge { display: inline-block; padding: 1px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-implemented { background: #d8f3dc; color: #2d6a4f; }
        .badge-approved { background: #d8f3dc; color: #2d6a4f; }
        .badge-review { background: #e0e7ff; color: #3730a3; }
        .badge-pending { background: #f3f4f6; color: #6b7280; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Innovation Analytics Report</h1>
        <p>Generated {{ now()->format('F j, Y') }}</p>
    </div>

    <div class="section">
        <h2>Overview</h2>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="value">{{ $stats['total_ideas'] }}</div>
                <div class="label">Total Ideas</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ $stats['in_review'] }}</div>
                <div class="label">In Review</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ $stats['implemented'] }}</div>
                <div class="label">Implemented</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Category Distribution</h2>
        @foreach ($categories as $cat)
            <div style="margin-bottom: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 11px;">
                    <span>{{ $cat['name'] }}</span>
                    <span><strong>{{ $cat['percentage'] }}%</strong> ({{ $cat['count'] }})</span>
                </div>
                <div class="category-bar">
                    <div class="category-bar-fill" style="width: {{ $cat['percentage'] }}%;"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="section">
        <h2>Top Performing Ideas</h2>
        @if (count($topIdeas) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Creator</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topIdeas as $idea)
                        <tr>
                            <td><strong>{{ $idea->title }}</strong></td>
                            <td>{{ $idea->user->name ?? 'Unknown' }}</td>
                            <td>{{ $idea->category }}</td>
                            <td><span class="badge badge-{{ strtolower(str_replace(' ', '-', $idea->status)) }}">{{ $idea->status }}</span></td>
                            <td>{{ $idea->created_at->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #888; font-size: 11px;">No ideas submitted yet.</p>
        @endif
    </div>

    <div class="footer">
        Employee Suggestion Box &mdash; Analytics Report &mdash; {{ now()->format('Y') }}
    </div>
</body>
</html>
