<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'quvchilar Statistikasi</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 15px;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 8px 0;
            color: #333;
        }

        .header-info {
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
            font-size: 9px;
        }

        .student-name {
            text-align: left !important;
            font-weight: bold;
        }

        .row-number {
            width: 30px;
        }

        .student-name-col {
            width: 180px;
        }

        .score-col {
            width: 90px;
        }

        .total-col {
            width: 70px;
        }

        .bsb-score {
            color: #1e40af;
            font-weight: bold;
        }

        .chsb-score {
            color: #059669;
            font-weight: bold;
        }

        .overall-excellent {
            background-color: #dcfce7;
            color: #166534;
            font-weight: bold;
        }

        .overall-good {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: bold;
        }

        .overall-poor {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }

        .no-data {
            color: #9ca3af;
            font-style: italic;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $sinf->name }} sinf - {{ $subject->name }} fani natijalari</h1>
        <div class="header-info">
            {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }} davri |
            Jami: {{ count($studentsData) }} nafar o'quvchi
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="row-number">№</th>
                <th class="student-name-col">O'quvchi F.I.Sh.</th>
                <th class="score-col">BSB</th>
                <th class="score-col">CHSB</th>
                <th class="total-col">Umumiy</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentsData as $index => $student)
                <tr>
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td class="student-name">{{ $student['full_name'] }}</td>
                    <td class="score-col">
                        @if($student['bsb']['total'] > 0)
                            <span class="bsb-score">{{ $student['bsb']['total'] }} / {{ $student['bsb']['percentage'] }}%</span>
                        @else
                            <span class="no-data">-</span>
                        @endif
                    </td>
                    <td class="score-col">
                        @if($student['chsb']['total'] > 0)
                            <span class="chsb-score">{{ $student['chsb']['total'] }} / {{ $student['chsb']['percentage'] }}%</span>
                        @else
                            <span class="no-data">-</span>
                        @endif
                    </td>
                    <td class="total-col
                        @if($student['overall_total'] >= 80)
                            overall-excellent
                        @elseif($student['overall_total'] >= 60)
                            overall-good
                        @elseif($student['overall_total'] > 0)
                            overall-poor
                        @endif">
                        @if($student['overall_total'] > 0)
                            {{ $student['overall_total'] }}%
                        @else
                            <span class="no-data">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Yaratilgan: {{ $generatedAt }}
    </div>
</body>
</html>

