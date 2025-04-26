@props(['problems' => 5, 'students' => 10])
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
                @while($problems > 0)
                    <x-columnblock />
                    {{$problems-=1}}
                @endwhile
                <th>Average</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Emma Johnson</td>
                <td>
                    <x-rowblock />
                </td>
                <td>
                    <x-rowblock />
                </td>
                <td>
                    <x-rowblock />
                </td>
                <td>
                    <x-rowblock />
                </td>
                <td>
                    <x-rowblock />
                </td>
                <td><span class="average high">88%</span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
