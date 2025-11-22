<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Imtihon Natijalari</title>
    <style>
        /* Add this CSS to your stylesheet */
            /* QR Code and exam info section - Bottom Right */
        .exam-footer {
            position: fixed;
            bottom: 15px;
            right: 15px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-end;
            gap: 15px;
            font-size: 16px;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 10px;
        }

        .exam-code {
            white-space: nowrap;
            line-height: 1;
        }

        .exam-code strong {
            display: inline;
            margin-right: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .exam-code span {
            font-size: 16px;
            font-weight: normal;
        }

        .qr-code img {
            width: 120px;
            height: 120px;
            display: block;
        }
        @page {
            size: A4 landscape;
            margin: 6mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 14px;
            text-align: center;
            margin-bottom: 5px;
        }

        .exam-info {
            font-size: 10px;
            margin-bottom: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 5px;
        }

        th, td {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
            font-size: 7px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            line-height: 1.1;
        }

        /* Optimized column widths for better fit */
        .col-number {
            width: 15px; /* Minimized № column */
        }

        .col-name {
            width: 180px; /* Much wider F.I.Sh. column */
            text-align: left;
            padding-left: 3px;
            font-size: 6px;
        }

        .col-problem {
            width: 25px; /* Smaller problem columns to fit more content */
        }

        .col-total {
            width: 35px; /* Jami ball column */
            font-weight: bold;
        }

        .col-percentage {
            width: 35px; /* Foiz (%) column */
        }

        /* Row styling */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tfoot tr {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        tfoot td {
            font-size: 6px;
        }


        .problem-header {
            font-size: 6px;
            line-height: 1.0;
        }

        .max-mark {
            font-size: 5px;
            color: #666;
        }

        /* Make student names wrap properly */
        .student-name {
            line-height: 1.1;
            word-break: break-word;
            hyphens: auto;
        }

        /* Ensure numbers and scores are clearly visible */
        .score {
            font-weight: bold;
            font-size: 7px;
        }
    </style>
</head>
<body>
<h1>Imtihon natijalari</h1>
<div class="exam-info">
    <strong>Sinf:</strong> {{ $exam->sinf->name ?? 'Nomaʼlum' }} &nbsp;&nbsp;&nbsp;
    <strong>Fan:</strong> {{ $exam->subject->name ?? 'Nomaʼlum' }} &nbsp;&nbsp;&nbsp;
    <strong>Imtihon:</strong> {{ $exam->serial_number }} - {{ $exam->type }}
</div>

<table>
    <thead>
    <tr>
        <th class="col-number">№</th>
        <th class="col-name">F.I.Sh.</th>
        @foreach($problems as $problem)
            <th class="col-problem">
                <div class="problem-header">
                    {{ $problem['id'] ?? '' }}<br>
                    <span class="max-mark">({{ $problem['max_mark'] ?? '' }})</span>
                </div>
            </th>
        @endforeach
        <th class="col-total">
            <div class="problem-header">
                Jami ball<br>
                <span class="max-mark">({{ $totalMaxScore }})</span>
            </div>
        </th>
        <th class="col-percentage">Foiz (%)</th>
    </tr>
    </thead>
    <tbody>
    @php
        $problemTotals = [];
        $problemCounts = [];
        $totalScores = [];
    @endphp
    @foreach($students as $index => $student)
        @php
            $name = $student->extractFirstAndLastName($student->full_name);

            // Get pre-calculated values from pivot table if available
            $pivotData = $student->exams->first()?->pivot ?? null;

            $overall = $pivotData ? $pivotData->total : 0;
            $percentage = $pivotData ? $pivotData->percentage : 0;

            // If no pivot data, fall back to manual calculation (for backwards compatibility)
            if (!$pivotData) {
                $overall = 0;
                foreach($problems as $problem) {
                    $mark = $marks->first(function ($m) use ($student, $problem) {
                        return $m->student_id == $student->id && $m->problem_id == $problem['id'];
                    });
                    $overall += $mark->mark ?? 0;
                }
                $percentage = $totalMaxScore > 0 ? round(($overall / $totalMaxScore) * 100, 1) : 0;
            }

            $totalScores[] = $overall;
        @endphp
        <tr>
            <td class="col-number">{{ $index + 1 }}</td>
            <td class="col-name student-name"> {{ $name['first'] }} {{ $name['last'] }} </td>
            @foreach($problems as $problem)
                @php
                    $mark = $marks->first(function ($m) use ($student, $problem) {
                        return $m->student_id == $student->id && $m->problem_id == $problem['id'];
                    });
                    $score = $mark->mark ?? 0;
                    $problemTotals[$problem['id']] = ($problemTotals[$problem['id']] ?? 0) + $score;
                    $problemCounts[$problem['id']] = ($problemCounts[$problem['id']] ?? 0) + 1;
                @endphp
                <td class="col-problem score">{{ $score }}</td>
            @endforeach

            <td class="col-total score">{{ $overall }}</td>
            <td class="col-percentage score">{{ $percentage }}%</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2"><strong>O'rtacha ball</strong></td>
        @foreach ($problems as $problem)
            @php
                $avg = isset($problemTotals[$problem['id']]) && $problemCounts[$problem['id']] > 0
                    ? round($problemTotals[$problem['id']] / $problemCounts[$problem['id']], 1)
                    : 0;
            @endphp
            <td class="col-problem">{{ $avg }}</td>
        @endforeach
        @php
            $avgTotal = count($totalScores) > 0 ? round(array_sum($totalScores) / count($totalScores), 1) : 0;
            $avgPercentage = $totalMaxScore > 0 ? round(($avgTotal / $totalMaxScore) * 100, 1) : 0;
        @endphp
        <td class="col-total"><strong>{{ $avgTotal }}</strong></td>
        <td class="col-percentage"><strong>{{ $avgPercentage }}%</strong></td>
    </tr>
    <tr>
        <td colspan="2"><strong>O'zlashtirish foizi (%)</strong></td>
        @foreach ($problems as $problem)
            @php
                $avg = isset($problemTotals[$problem['id']]) && $problemCounts[$problem['id']] > 0
                    ? round($problemTotals[$problem['id']] / $problemCounts[$problem['id']], 1)
                    : 0;
                $mastery = $problem['max_mark'] > 0
                    ? round(($avg / $problem['max_mark']) * 100, 1)
                    : 0;
            @endphp
            <td class="col-problem">{{ $mastery }}%</td>
        @endforeach
        <td class="col-total" colspan="2"><strong>{{ $avgPercentage }}%</strong></td>
    </tr>
    </tfoot>
</table>

<div class="exam-footer">
    <div class="exam-code">
        <strong>Kod:</strong>
        {{ $exam->code ?? $exam->id }}
    </div>
    <div class="qr-code">
        <img src="{{ public_path('qrcode.png') }}" alt="QR Code">
    </div>
</div>

</body>
</html>
