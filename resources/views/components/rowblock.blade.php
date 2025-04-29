@props(['mark','max_score'])
<div class="score ">
    <span class="score-value">{{ $mark }} / {{$max_score }}</span>
    
    @php
        $avarage_score = ($mark * 100) / 12;
    @endphp
    
    <div class="progress-bar">
        <div class="progress" style="width: {{ $avarage_score }}%"></div>
    </div>
</div>
