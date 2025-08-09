<x-filament-widgets::widget xmlns:x-filament="http://www.w3.org/1999/html">
    {{-- This root div stacks the sections vertically with a gap. --}}
    <div class="space-y-6">

        {{-- Section 1: The redesigned, prominent School Info block. --}}
        <x-filament::section>
            {{-- Use the 'header-actions' slot to add a button or link if needed --}}
            <x-slot name="header-actions">
                <x-filament::link
                    href="#"
                    icon="heroicon-m-book-open"
                >
                    Qo'llanma
                </x-filament::link>
            </x-slot>

            {{-- This div creates the layout: Icon on the left, text on the right --}}
            <div class="flex items-center gap-x-6">
                {{-- Prominent Icon --}}
                <div class="flex-shrink-0">
                    <div class="rounded-full bg-blue-100 p-4 dark:bg-blue-500/20">
                        <x-heroicon-o-academic-cap class="h-8 w-8 text-blue-500 dark:text-blue-400" />
                    </div>
                </div>
                {{-- Text Content --}}
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ auth()->user()->maktab->name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Siz tizimga ushbu maktab foydalanuvchisi sifatida kirgansiz. Barcha ma'lumotlar shu maktabga tegishli bo'ladi.
                    </p>
                </div>
            </div>
        </x-filament::section>

        {{-- Section 2: The interactive Exam Report table. --}}
        <x-filament::section>
            @push('styles')
                <link rel="stylesheet" href="{{ asset('css/app.css') }}">
            @endpush
            @livewire('dashboard')
        </x-filament::section>

    </div>
</x-filament-widgets::widget>
