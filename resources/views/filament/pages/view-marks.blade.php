<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            Saqlash
        </x-filament::button>
    </form>
</x-filament::page>
