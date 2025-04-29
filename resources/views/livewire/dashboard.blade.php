@props(['marks', 'marks_count', 'problems', 'students'])
<div>
    <div class="controls">
        <div class="exam-selector">
            <label for="exam-select">Select Exam</label>
            <div class="select-wrapper">
                <select id="exam-select">
                    <option value="midterm">Mid-Term Examination 2025</option>
                    <option value="final">Final Examination 2025</option>
                    <option value="contest">Programming Contest 2025</option>
                </select>
            </div>
            <button id="generate-table" class="generate-btn">
                <svg class="generate-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"></path>
                    <path d="M21 3v5h-5"></path>
                </svg>
                Generate Table
            </button>
        </div>

        <div class="search-box">
            <input type="text" placeholder="Search students...">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
    </div>

    <div class="table-container">
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    @php
                        $over = 0;
                    @endphp
                    @foreach($problems as $problem)
                        @php
                            $over+=$problem->max_mark;
                        @endphp
                        <x-columnblock :problemNum="$problem->problem_number" :maxScore="$problem->max_mark"/>
                    @endforeach
                    <th>Overall <br> <span class="mr-2 text-center">({{ $over }})</span> </th>
                    
                    <th>Average</th>
                </tr>
            </thead>

            <tbody>
                
                @foreach ($students as $student)
                    <tr>
                        @php $overall = 0; @endphp
                        <td>{{ $student->full_name }}</td>
                        @foreach($marks as $mark)
                            @if ($student->id === $mark->student->id)
                                @php
                                    $overall += $mark->mark;
                                @endphp
                                <td>
                                    <x-rowblock :mark="$mark->mark" :max_score="$mark->problem->max_mark" />
                                </td>
                            @endif
                        @endforeach
                        
                        <td><span class="average ">{{ $overall }}</span></td>
                        <td><span class="average ">88%</span></td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
