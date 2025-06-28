<x-filament-widgets::widget>
    <x-filament::section>

        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Maktab Ma’lumotingiz</h2>


        <div class="mt-2 flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
            <p>
                Siz hozirda quyidagi maktab foydalanuvchisi sifatida tizimga kirgansiz:
            </p>
        </div>



        <div class="mt-2 flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-md text-gray-900 dark:text-gray-100 font-medium">
            <x-heroicon-o-academic-cap class="w-5 h-5 text-blue-500 dark:text-blue-400" />
            {{ auth()->user()->maktab->name }}
        </div>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Sizga ko‘rsatilayotgan barcha ma’lumotlar — masalan, o‘quvchilar, imtihonlar va hisobotlar — aynan shu maktabga tegishli bo‘ladi.
        </p>
    </x-filament::section>


    <br>
    <x-filament::section>
        @push('styles')
            <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @endpush
        @livewire('dashboard')
    </x-filament::section>

</x-filament-widgets::widget>
