<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Daatio</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #2D3748;
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1A4E8C;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .print-header h1 { font-size: 1.5rem; color: #1A4E8C; }
        .print-date { font-size: 0.8rem; color: #718096; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        th, td {
            text-align: left;
            padding: 0.625rem 0.75rem;
            border-bottom: 1px solid #E2E8F0;
            font-size: 0.85rem;
        }
        th {
            background: #F7F8FA;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #718096;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px,1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 1rem;
        }
        .stat-label { font-size: 0.75rem; color: #718096; text-transform: uppercase; margin-bottom: 0.25rem; }
        .stat-value { font-size: 1.25rem; font-weight: 700; }
        .btn-print {
            background: #1A4E8C;
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .btn-print:hover { background: #153E75; }
        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
    @yield('content')
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
