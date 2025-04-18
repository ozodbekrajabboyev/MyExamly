<div>
    <form wire:submit.prevent="saveMarks">
        {{ $this->form }}

        <div class="flex justify-end mt-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Baholarni saqlash
            </button>
        </div>
    </form>

    @if (session('message'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif
</div>
