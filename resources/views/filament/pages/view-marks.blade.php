<x-filament::page>
    <x-filament::card>
        <div class="px-4 py-2">
            <h2 class="text-xl font-bold">{{ $exam->subject->name }} - {{ $exam->type }}</h2>
            <p class="text-gray-600">Sinf: {{ $exam->sinf->name }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">O'quvchi</th>
                    @foreach($problems as $problem)
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            {{ $problem->name }}
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($students as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $student->name }}
                        </td>
                        @foreach($problems as $problem)
                            @php
                                $mark = $marks->firstWhere(fn($m) =>
                                    $m->student_id == $student->id &&
                                    $m->problem_id == $problem->id
                                );
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $mark->mark ?? '-' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::card>
</x-filament::page>
