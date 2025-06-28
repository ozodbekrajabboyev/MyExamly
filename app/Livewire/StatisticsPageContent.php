<?php
// File: app/Livewire/StatisticsPageContent.php
namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Sinf;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StatisticsPageContent extends Component
{
    public ?int $sinfId = null;
    public ?int $subjectId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    /**
     * The mount method is called when the component is first initialized.
     * We'll set a default date range here (e.g., last 7 days).
     */
    public function mount(): void
    {
        $this->startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    /**
     * This is the core method. When called, it dispatches the 'updateStats' event
     * with the current filter values as the payload. The ChartWidget will listen for this.
     * We use a specific event name to avoid conflicts.
     */
    public function applyFilters(): void
    {
        $this->dispatch('updateStats',
            sinfId: $this->sinfId,
            subjectId: $this->subjectId,
            startDate: $this->startDate,
            endDate: $this->endDate
        );
    }

    /**
     * Quick filter method for setting the date range to the last 7 days.
     */
    public function filterLast7Days(): void
    {
        $this->startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->applyFilters(); // Re-apply filters after setting dates
    }

    /**
     * Quick filter method for setting the date range to the last 30 days.
     */
    public function filterLast30Days(): void
    {
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->applyFilters();
    }

    /**
     * Quick filter method for setting the date range to the last 3 months.
     */
    public function filterLast3Months(): void
    {
        $this->startDate = Carbon::now()->subMonths(3)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->applyFilters();
    }

    /**
     * The render method returns the component's view and passes any
     * necessary data to it, like the lists for the dropdowns.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.statistics-page-content', [
            'sinfs' => Sinf::query()->pluck('name', 'id'),
            'subjects' => Subject::query()->pluck('name', 'id'),
        ]);
    }
}
