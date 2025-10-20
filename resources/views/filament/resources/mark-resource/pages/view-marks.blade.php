<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Exam Selection Form -->
        <div class="bg-white shadow rounded-lg p-6">
            {{ $this->form }}
        </div>

        @if($selectedExam && $reportData)
            <!-- Exam Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="border-b pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Imtihon ma'lumotlari</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $selectedExam->subject->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sinf</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $selectedExam->sinf->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Imtihon turi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $selectedExam->serial_number }}-{{ $selectedExam->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">O'qituvchi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $selectedExam->teacher->full_name }}</dd>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Jami o'quvchilar</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $reportData['statistics']['total_students'] }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Maksimal ball</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $reportData['statistics']['max_possible'] }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">O'rtacha ball</div>
                    <div class="mt-1 text-2xl font-semibold text-blue-600">{{ $reportData['statistics']['average'] }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Eng yuqori ball</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600">{{ $reportData['statistics']['highest'] }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Eng past ball</div>
                    <div class="mt-1 text-2xl font-semibold text-red-600">{{ $reportData['statistics']['lowest'] }}</div>
                </div>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Muvaffaqiyat</div>
                    <div class="mt-1 text-2xl font-semibold text-purple-600">{{ $reportData['statistics']['pass_rate'] }}%</div>
                </div>
            </div>

            <!-- Problem Statistics -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="border-b pb-4 mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Topshiriqlar bo'yicha statistika</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topshiriq</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max ball</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O'rtacha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eng yuqori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eng past</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urinishlar</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reportData['problem_statistics'] as $stat)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Topshiriq {{ $stat['problem_id'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat['max_mark'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat['average'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $stat['highest'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ $stat['lowest'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat['total_attempts'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Student Results -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="border-b pb-4 mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Batafsil natijalar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O'quvchi</th>
                            @foreach($reportData['problems'] as $problem)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    T{{ $problem['id'] }}<br>
                                    <small>({{ $problem['max_mark'] }})</small>
                                </th>
                            @endforeach
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jami</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Holat</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reportData['students'] as $studentResult)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $studentResult['student']->full_name }}
                                </td>
                                @foreach($studentResult['marks'] as $mark)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($mark['percentage'] >= 80) bg-green-100 text-green-800
                                                @elseif($mark['percentage'] >= 60) bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $mark['mark'] }}
                                            </span>
                                    </td>
                                @endforeach
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-center text-gray-900">
                                    {{ $studentResult['total_mark'] }}/{{ $studentResult['max_total_mark'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($studentResult['percentage'] >= 80) bg-green-100 text-green-800
                                            @elseif($studentResult['percentage'] >= 60) bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $studentResult['percentage'] }}%
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($studentResult['status'] === 'Muvaffaqiyatli') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $studentResult['status'] }}
                                        </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
