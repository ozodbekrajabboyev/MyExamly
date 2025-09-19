<?php

namespace App\Livewire;

use App\Models\Teacher;
use App\Models\Region;
use App\Models\District;
use App\Models\Maktab;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class TeacherStatistics extends Component
{
    use WithPagination;

    public $selectedRegion = null;
    public $selectedDistrict = null;
    public $selectedMaktab = null;
    public $selectedMalakaDaraja = null;
    public $search = '';

    public $regions = [];
    public $districts = [];
    public $maktabs = [];
    
    protected $queryString = [
        'selectedRegion' => ['except' => null],
        'selectedDistrict' => ['except' => null],
        'selectedMaktab' => ['except' => null],
        'selectedMalakaDaraja' => ['except' => null],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->regions = Region::orderBy('name')->get();
        $this->loadDistricts();
        $this->loadMaktabs();
    }

    public function updatedSelectedRegion()
    {
        $this->selectedDistrict = null;
        $this->selectedMaktab = null;
        $this->loadDistricts();
        $this->loadMaktabs();
        $this->resetPage();
    }

    public function updatedSelectedDistrict()
    {
        $this->selectedMaktab = null;
        $this->loadMaktabs();
        $this->resetPage();
    }

    public function updatedSelectedMaktab()
    {
        $this->resetPage();
    }

    public function updatedSelectedMalakaDaraja()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function loadDistricts()
    {
        if ($this->selectedRegion) {
            $this->districts = District::where('region_id', $this->selectedRegion)
                ->orderBy('name')
                ->get();
        } else {
            $this->districts = collect();
        }
    }

    public function loadMaktabs()
    {
        if ($this->selectedDistrict) {
            $this->maktabs = Maktab::where('district_id', $this->selectedDistrict)
                ->orderBy('name')
                ->get();
        } elseif ($this->selectedRegion) {
            $this->maktabs = Maktab::whereHas('district', function (Builder $query) {
                $query->where('region_id', $this->selectedRegion);
            })->orderBy('name')->get();
        } else {
            $this->maktabs = collect();
        }
    }

    public function clearFilters()
    {
        $this->selectedRegion = null;
        $this->selectedDistrict = null;
        $this->selectedMaktab = null;
        $this->selectedMalakaDaraja = null;
        $this->search = '';
        $this->districts = collect();
        $this->maktabs = collect();
        $this->resetPage();
    }

    public function getTeachersQuery()
    {
        $query = Teacher::with(['maktab.district.region', 'user', 'subjects']);

        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function (Builder $userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->selectedRegion) {
            $query->whereHas('maktab.district.region', function (Builder $q) {
                $q->where('id', $this->selectedRegion);
            });
        }

        if ($this->selectedDistrict) {
            $query->whereHas('maktab.district', function (Builder $q) {
                $q->where('id', $this->selectedDistrict);
            });
        }

        if ($this->selectedMaktab) {
            $query->where('maktab_id', $this->selectedMaktab);
        }

        if ($this->selectedMalakaDaraja) {
            $query->where('malaka_toifa_daraja', $this->selectedMalakaDaraja);
        }

        return $query;
    }

    public function getStatistics()
    {
        $query = $this->getTeachersQuery();
        
        $totalTeachers = $query->count();
        
        $malakaStats = $query->selectRaw('malaka_toifa_daraja, COUNT(*) as count')
            ->groupBy('malaka_toifa_daraja')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->malaka_toifa_daraja ?: 'Belgilanmagan' => $item->count];
            });

        $regionStats = $query->join('maktabs', 'teachers.maktab_id', '=', 'maktabs.id')
            ->join('districts', 'maktabs.district_id', '=', 'districts.id')
            ->join('regions', 'districts.region_id', '=', 'regions.id')
            ->selectRaw('regions.name, COUNT(*) as count')
            ->groupBy('regions.name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => $item->count];
            });

        return [
            'total' => $totalTeachers,
            'malaka_stats' => $malakaStats,
            'region_stats' => $regionStats,
        ];
    }

    public function getMalakaOptions()
    {
        return [
            '1-toifa' => '1-toifa',
            '2-toifa' => '2-toifa',
            'oliy-toifa' => 'Oliy toifa',
            'mutaxasis' => 'Mutaxasis',
        ];
    }

    public function render()
    {
        $teachers = $this->getTeachersQuery()->paginate(10);
        $statistics = $this->getStatistics();

        return view('livewire.teacher-statistics', [
            'teachers' => $teachers,
            'statistics' => $statistics,
            'malakaOptions' => $this->getMalakaOptions(),
        ]);
    }
}