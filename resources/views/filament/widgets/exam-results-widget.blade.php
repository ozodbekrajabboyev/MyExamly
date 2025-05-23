<x-filament-widgets::widget>
    <x-filament::section>
        @push('styles')
            <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @endpush
        @livewire('dashboard')
    </x-filament::section>
</x-filament-widgets::widget>
