{{--
    This component uses Filament's <x-filament::section> for consistent styling
    with the rest of the Filament UI (padding, background, borders, etc.).
--}}
<x-filament::section>
    <div class="space-y-4">
        {{-- Filter Inputs --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            {{-- Sinf (Class) Dropdown --}}
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model="sinfId">
                    <option value="">All Classes</option>
                    @foreach($sinfs as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            {{-- Subject Dropdown --}}
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model="subjectId">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            {{-- Start Date Picker --}}
            <x-filament::input.wrapper>
                {{-- FIX: Changed from x-filament::input.date to x-filament::input with type="date" --}}
                <x-filament::input type="date" wire:model="startDate" placeholder="Start Date" />
            </x-filament::input.wrapper>

            {{-- End Date Picker --}}
            <x-filament::input.wrapper>
                {{-- FIX: Changed from x-filament::input.date to x-filament::input with type="date" --}}
                <x-filament::input type="date" wire:model="endDate" placeholder="End Date" />
            </x-filament::input.wrapper>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- Main "Apply" button --}}
            <div>
                <x-filament::button wire:click="applyFilters">
                    Apply Filters
                </x-filament::button>
            </div>

            {{-- Quick filter buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Quick Filters:</span>
                <x-filament::button color="gray" wire:click="filterLast7Days">
                    Last 7 days
                </x-filament::button>
                <x-filament::button color="gray" wire:click="filterLast30Days">
                    Last 30 days
                </x-filament::button>
                <x-filament::button color="gray" wire:click="filterLast3Months">
                    Last 3 months
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament::section>
