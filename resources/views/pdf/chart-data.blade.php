<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chart Data PDF</title>
</head>
<body>
    <h1>Chart Data</h1>
    <table style="width: 100%; border-collapse: collapse;" border="1">
        <thead>
            <tr>
                <th style="padding: 8px;">Date</th>
                <th style="padding: 8px;">Average Days</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chartData['labels'] as $index => $date)
                <tr>
                    <td style="padding: 8px;">{{ $date }}</td>
                    <td style="padding: 8px;">{{ $chartData['data'][$index] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
