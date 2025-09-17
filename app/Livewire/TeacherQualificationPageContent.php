<?php

namespace App\Livewire;

use App\Models\Region;
use App\Models\District;
use Livewire\Component;

class TeacherQualificationPageContent extends Component
{
    public ?int $regionId = null;
    public ?int $districtId = null;
    public $districts = [];

    /**
     * The mount method is called when the component is first initialized.
     */
    public function mount(): void
    {
        $this->districts = [];
    }

    /**
     * When region is updated, load districts for that region
     */
    public function updatedRegionId($value): void
    {
        if ($value) {
            $this->districts = District::where('region_id', $value)->pluck('name', 'id')->toArray();
            $this->districtId = null; // Reset district when region changes
        } else {
            $this->districts = [];
            $this->districtId = null;
        }

        $this->applyFilters();
    }

    /**
     * When district is updated, apply filters
     */
    public function updatedDistrictId(): void
    {
        $this->applyFilters();
    }

    /**
     * This method dispatches the 'updateTeacherStats' event
     * with the current filter values as the payload.
     */
    public function applyFilters(): void
    {
        $this->dispatch('updateTeacherStats',
            regionId: $this->regionId,
            districtId: $this->districtId
        );
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->regionId = null;
        $this->districtId = null;
        $this->districts = [];
        $this->applyFilters();
    }

    /**
     * The render method returns the component's view
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.teacher-qualification-page-content', [
            'regions' => Region::query()->pluck('name', 'id'),
        ]);
    }
}
