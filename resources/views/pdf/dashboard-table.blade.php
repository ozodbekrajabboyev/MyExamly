<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Imtihon Natijalari</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; }
        th { background-color: #eee; }
        p {font-size: 13px}
    </style>
</head>
<body>
<h2>Imtihon natijalari</h2>
<p><strong>Sinf:</strong> {{ $exam->sinf->name ?? 'Nomaʼlum' }}</p>
<p><strong>Fan:</strong> {{ $exam->subject->name ?? 'Nomaʼlum' }}</p>
<p><strong>Imtihon:</strong> {{ $exam->serial_number }} - {{ $exam->type }}</p>

<table>
    <thead>
    <tr>
        <th>№</th>
        <th style="text-align: left;">F.I.Sh.</th>
        @foreach($problems as $problem)
            <th>{{ $problem->problem_number }}-topshiriq<br><small>({{ $problem->max_mark }})</small></th>
        @endforeach
        <th>Jami<br><small>({{ $totalMaxScore }})</small></th>
        <th>Foiz (%)</th>
    </tr>
    </thead>
    <tbody>
    @php
        $problemTotals = [];
        $problemCounts = [];
        $totalScores = [];
    @endphp

    @foreach($students as $index => $student)
        @php $overall = 0; @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td style="text-align: left;">{{ $student->full_name }}</td>

            @foreach($problems as $problem)
                @php
                    $mark = $marks->first(function ($m) use ($student, $problem) {
                        return $m->student_id == $student->id && $m->problem_id == $problem->id;
                    });
                    $score = $mark->mark ?? 0;

                    $overall += $score;

                    $problemTotals[$problem->id] = ($problemTotals[$problem->id] ?? 0) + $score;
                    $problemCounts[$problem->id] = ($problemCounts[$problem->id] ?? 0) + 1;
                @endphp
                <td>{{ $score }}</td>
            @endforeach


            <td><strong>{{ $overall }}</strong></td>

            @php
                $percentage = $totalMaxScore > 0 ? round(($overall / $totalMaxScore) * 100, 1) : 0;
                $totalScores[] = $overall;
            @endphp
            <td>{{ $percentage }}%</td>
        </tr>
    @endforeach
    </tbody>

    <tfoot>
    <tr>
        <td colspan="2"><strong>O'rtacha ball</strong></td>
        @foreach ($problems as $problem)
            @php
                $avg = isset($problemTotals[$problem->id]) && $problemCounts[$problem->id] > 0
                    ? round($problemTotals[$problem->id] / $problemCounts[$problem->id], 1)
                    : 0;
            @endphp
            <td>{{ $avg }}</td>
        @endforeach

        @php
            $avgTotal = count($totalScores) > 0 ? round(array_sum($totalScores) / count($totalScores), 1) : 0;
            $avgPercentage = $totalMaxScore > 0 ? round(($avgTotal / $totalMaxScore) * 100, 1) : 0;
        @endphp

        <td rowspan="2"><strong>{{ $avgTotal }}</strong></td>
        <td rowspan="2"><strong>{{ $avgPercentage }}%</strong></td>
    </tr>
    <tr>
        <td colspan="2"><strong>O'zlashtirish foizi (%)</strong></td>
        @foreach ($problems as $problem)
            @php
                $avg = isset($problemTotals[$problem->id]) && $problemCounts[$problem->id] > 0
                    ? round($problemTotals[$problem->id] / $problemCounts[$problem->id], 1)
                    : 0;
                $mastery = $problem->max_mark > 0
                    ? round(($avg / $problem->max_mark) * 100, 1)
                    : 0;
            @endphp
            <td>{{ $mastery }}%</td>
        @endforeach
    </tr>
    </tfoot>
</table>
<br><br>
<h3><strong>Maktab-internatining  O‘IBDO‘:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;F.F.Raxmonov</h3>
<h3><strong>Metodbirlashma rahbari:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$exam->metod->full_name ?? "Noma'lum"}}</h3>
<h3><strong>Fan o‘qituvchisi:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$exam->teacher->full_name ?? "Noma'lum"}}</h3>
</body>
</html>
