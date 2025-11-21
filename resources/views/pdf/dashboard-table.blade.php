<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Imtihon Natijalari</title>
    <style>
        /* Add this CSS to your stylesheet */
        .signatures {
            margin-top: 20px;
        }

        .signature-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            line-height: 1;
        }

        .signature-item strong {
            width: 200px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .signature-item img {
            margin-right: 10px;
            max-height: 30px;
        }

        .signature-item .signature-line {
            width: 100px;
            margin-right: 10px;
            border-bottom: 1px solid #000;
            height: 1px;
            display: inline-block;
        }

        .signature-item span {
            flex: 1;
            margin-left: 10px;
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

        .signatures {
            margin-top: 10px;
            font-size: 9px;
            page-break-inside: avoid;
        }

        .signatures h3 {
            margin: 5px 0;
            font-size: 9px;
            display: flex;
            align-items: center;
        }

        .signatures img {
            width: 40px;
            height: auto;
            margin: 0 8px;
            vertical-align: middle;
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

<div class="signatures">
    <?php
    $user = $exam->teacher;
    $admin = App\Models\User::where('maktab_id', $user->maktab_id)->where('role_id', 2)->first();
    ?>

    <div class="signature-item">
        <strong>Maktab-internatining O'IBDO':</strong>
        @if($admin && $admin->signature_path)
            <img src="{{ public_path('/storage/' . $admin->signature_path) }}" alt="Signature" style="position: relative; top: 5px;">
        @else
            <span class="signature-line"></span>
        @endif
        <span>{{ App\Models\User::where('maktab_id', auth()->user()->maktab_id)->where('role_id', 2)->pluck('name')[0] }}</span>
    </div>

{{--    This section is temporarily removed as per the client's need --}}
{{--    <div class="signature-item">--}}
{{--        <strong>Metodbirlashma rahbari:</strong>--}}
{{--        @if($exam->metod && $exam->metod->signature_path)--}}
{{--            <img src="{{ public_path('/storage/' . $exam->metod->signature_path) }}" alt="Signature" style="position: relative; top: 5px;">--}}
{{--        @else--}}
{{--            <span class="signature-line"></span>--}}
{{--        @endif--}}
{{--        <span>{{$exam->metod->full_name}}</span>--}}
{{--    </div>--}}

    <div class="signature-item">
        <strong>Fan o'qituvchisi:</strong>
        @if($exam->teacher && $exam->teacher->signature_path)
            <img src="{{ public_path('/storage/' . $exam->teacher->signature_path) }}" alt="Signature" style="position: relative; top: 5px;">
        @else
            <span class="signature-line"></span>
        @endif
        <span>{{$exam->teacher->full_name}}</span>
    </div>

    @if($exam->teacher2)
        <div class="signature-item">
            <strong>Fan o'qituvchisi:</strong>
            @if($exam->teacher2->signature_path)
                <img src="{{ public_path('/storage/' . $exam->teacher2->signature_path) }}" alt="Signature" style="position: relative; top: 5px;">
            @else
                <span class="signature-line"></span>
            @endif
            <span>{{$exam->teacher2->full_name}}</span>
        </div>
    @endif
</div>
</body>
</html>
