<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Exam Information Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Imtihon ma'lumotlari</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <strong>Fan:</strong> {{ $this->exam->subject->name }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Sinf:</strong> {{ $this->exam->sinf->name }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Turi:</strong> {{ $this->exam->serial_number }} - {{ $this->exam->type }}
                    </p>
                </div>
                <div>
                    <h4 class="text-md font-medium text-gray-900">O'qituvchi</h4>
                    <p class="text-sm text-gray-600 mt-1">{{ $this->exam->teacher->full_name }}</p>
                    @if($this->exam->teacher2)
                        <p class="text-sm text-gray-600">{{ $this->exam->teacher2->full_name }}</p>
                    @endif
                </div>
                <div>
                    <h4 class="text-md font-medium text-gray-900">Statistika</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        <strong>O'quvchilar:</strong> {{ $this->students->count() }} ta
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Topshiriqlar:</strong> {{ count($this->problems) }} ta
                    </p>
                </div>
            </div>
        </div>

        <!-- Problems Summary -->
        <div class="bg-blue-50 rounded-lg p-4">
            <h4 class="text-md font-medium text-blue-900 mb-3">Topshiriqlar ro'yxati</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($this->problems as $problem)
                    <div class="bg-white rounded p-3 text-center border border-blue-200">
                        <div class="text-sm font-semibold text-blue-900">T-{{ $problem['id'] }}</div>
                        <div class="text-xs text-blue-600">Max: {{ $problem['max_mark'] }} ball</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Marks Form -->
        @if($this->students->count() > 0 && count($this->problems) > 0)
            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6 flex justify-end space-x-3">
                    <x-filament::button
                        wire:click="save"
                        color="success"
                        icon="heroicon-o-check"
                        size="lg"
                    >
                        Baholarni saqlash
                    </x-filament::button>
                </div>
            </form>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <div class="text-yellow-800">
                    @if($this->students->count() === 0)
                        <h3 class="text-lg font-medium mb-2">Sinfda o'quvchilar yo'q</h3>
                        <p>Baholarni kiritish uchun avval sinfga o'quvchilar qo'shing.</p>
                    @elseif(count($this->problems) === 0)
                        <h3 class="text-lg font-medium mb-2">Imtihonda topshiriqlar yo'q</h3>
                        <p>Baholarni kiritish uchun avval imtihonga topshiriqlar qo'shing.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Statistics (Optional Enhancement) -->
    @if($this->students->count() > 0 && count($this->problems) > 0)
        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <h4 class="text-md font-medium text-gray-900 mb-3">Tez statistika</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-white rounded p-3">
                    <div class="text-2xl font-bold text-green-600">{{ $this->students->count() }}</div>
                    <div class="text-sm text-gray-600">O'quvchilar</div>
                </div>
                <div class="bg-white rounded p-3">
                    <div class="text-2xl font-bold text-blue-600">{{ count($this->problems) }}</div>
                    <div class="text-sm text-gray-600">Topshiriqlar</div>
                </div>
                <div class="bg-white rounded p-3">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ collect($this->problems)->sum('max_mark') }}
                    </div>
                    <div class="text-sm text-gray-600">Maksimal
                        Addball</div>
                </div>
                <div class="bg-white rounded p-3">
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ $this->students->count() * count($this->problems) }}
                    </div>
                    <div class="text-sm text-gray-600">Jami baholar</div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
