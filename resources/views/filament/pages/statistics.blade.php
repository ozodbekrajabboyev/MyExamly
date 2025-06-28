<x-filament-panels::page>
    {{--
        This line embeds our custom Livewire component directly into the Filament page.
        Filament pages are essentially Blade views, allowing for this level of customization.
    --}}

    @livewire(App\Livewire\StatisticsPageContent::class)
</x-filament-panels::page>
