<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Imtihon Natijalari</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 6mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
            font-size: 7px;
            word-break: break-word;
        }
        th {
            background-color: #eee;
        }

        /* Column widths */
        th:first-child, td:first-child {
            width: 15px; /* № column */
        }
        th:last-child, td:last-child {
            width: 25px; /* Foiz (%) column */
        }

        h2 {
            font-size: 8px;
            margin-bottom: 2px;
        }
        p {
            font-size: 6px;
            margin: 1px 0;
        }

        .signatures {
            margin-top: 10px;
            font-size: 10px;
        }
        .signatures h3 {
            margin: 4px 0;
            font-size: 10px;
        }
        .signatures img {
            width: 60px;
            height: auto;
            margin-left: 5px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<h1>Imtihon natijalari</h1>
<p style="font-size: 10px">
    <strong>Sinf:</strong> {{ $exam->sinf->name ?? 'Nomaʼlum' }} &nbsp;&nbsp;&nbsp;
    <strong>Fan:</strong> {{ $exam->subject->name ?? 'Nomaʼlum' }} &nbsp;&nbsp;&nbsp;
    <strong>Imtihon:</strong> {{ $exam->serial_number }} - {{ $exam->type }}
</p>

<table>
    <thead>
    <tr style="margin: 30px">
        <th>№</th>
        <th style="width:25px">F.I.Sh.</th>
        @foreach($problems as $problem)
            <th style="width: 5px">
                {{ $problem['id'] ?? '' }}<br>
                <small>({{ $problem['max_mark'] ?? '' }})</small>
            </th>
        @endforeach

        <th style="width:25px;">Jami<br><small>({{ $totalMaxScore }})</small></th>
        <th style="width:25px;">Foiz (%)</th>
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
            <td style="width: 30px">{{ $student->full_name }}</td>
            @foreach($problems as $problem)
                @php
                    $mark = $marks->first(function ($m) use ($student, $problem) {
                        return $m->student_id == $student->id && $m->problem_id == $problem['id'];
                    });
                    $score = $mark->mark ?? 0;
                    $overall += $score;
                    $problemTotals[$problem['id']] = ($problemTotals[$problem['id']] ?? 0) + $score;
                    $problemCounts[$problem['id']] = ($problemCounts[$problem['id']] ?? 0) + 1;
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
                $avg = isset($problemTotals[$problem['id']]) && $problemCounts[$problem['id']] > 0
                    ? round($problemTotals[$problem['id']] / $problemCounts[$problem['id']], 1)
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
                $avg = isset($problemTotals[$problem['id']]) && $problemCounts[$problem['id']] > 0
                    ? round($problemTotals[$problem['id']] / $problemCounts[$problem['id']], 1)
                    : 0;
                $mastery = $problem['max_mark'] > 0
                    ? round(($avg / $problem['max_mark']) * 100, 1)
                    : 0;
            @endphp
            <td>{{ $mastery }}%</td>
        @endforeach
    </tr>
    </tfoot>
</table>

<div class="signatures">
    <?php
        $user = $exam->teacher;
        $admin = App\Models\User::where('maktab_id', $user->maktab_id)->where('role_id', 2)->first();
//        dd(public_path('signature.png'), public_path('/storage/'.$admin->signature_path));
//        dd(public_path());
    ?>
    <h3><strong>Maktab-internatining O'IBDO':</strong>
        <img src="{{ public_path('/storage/' . $admin->signature_path) }}">
        {{ App\Models\User::where('maktab_id', auth()->user()->maktab_id)->where('role_id', 2)->pluck('name')[0] }}
    </h3>
    <h3><strong>Metodbirlashma rahbari:</strong>
        <img src="{{ public_path('/storage/'. $exam->metod->signature_path) }}">
        {{$exam->metod->full_name ?? "Noma'lum"}}
    </h3>
    <h3><strong>Fan o'qituvchisi:</strong>
        <img src="{{ public_path('/storage/'. $exam->teacher->signature_path) }}">
        {{$exam->teacher->full_name ?? "Noma'lum"}}
    </h3>
</div>

</body>
</html>
