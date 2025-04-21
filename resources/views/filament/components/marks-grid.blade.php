@props([
    'students',
    'problems',
    'marks',
])

<div class="fi-modal-content overflow-y-auto p-4">
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-start text-sm font-semibold text-gray-600 dark:text-gray-300">
                    O'quvchi
                </th>
                @foreach($problems as $problem)
                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                        {{ $problem->name }}
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($students as $student)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="fi-ta-cell px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $student->name }}
                    </td>
                    @foreach($problems as $problem)
                        @php
                            $mark = $marks->firstWhere(fn($m) => $m->student_id == $student->id && $m->problem_id == $problem->id);
                        @endphp
                        <td class="fi-ta-cell px-3 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ $mark->mark ?? '-' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
