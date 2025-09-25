<?php

namespace App\Filament\Pages;

use App\Models\Teacher;
use App\Models\Region;
use App\Models\District;
use App\Models\Maktab;
use App\Filament\Widgets\MalakaStatisticsChart;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MalakaStatistikasi extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Umumiy statistika';

    protected static ?string $title = 'O\'qituvchilar malaka statistikasi';


    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.malaka-statistikasi';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filtrlar')
                    ->description('Statistika ma\'lumotlarini filtrlash uchun kerakli parametrlarni tanlang')
                    ->icon('heroicon-o-funnel')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('region_id')
                                    ->label('Viloyat')
                                    ->placeholder('Viloyatni tanlang')
                                    ->options(Region::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->form->fill([
                                            ...$this->form->getState(),
                                            'district_id' => null,
                                            'maktab_id' => null,
                                        ]);
                                        $this->resetTable();
                                    }),

                                Select::make('district_id')
                                    ->label('Tuman/Shahar')
                                    ->placeholder('Tumanni tanlang')
                                    ->options(function (callable $get) {
                                        $regionId = $get('region_id');
                                        if (!$regionId) {
                                            return [];
                                        }
                                        return District::where('region_id', $regionId)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->form->fill([
                                            ...$this->form->getState(),
                                            'maktab_id' => null,
                                        ]);
                                        $this->resetTable();
                                    }),

                                Select::make('maktab_id')
                                    ->label('Ta\'lim muassasasi')
                                    ->placeholder('Maktabni tanlang')
                                    ->options(function (callable $get) {
                                        $districtId = $get('district_id');
                                        if (!$districtId) {
                                            return [];
                                        }
                                        return Maktab::where('district_id', $districtId)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetTable()),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('search')
                                    ->label('Qidirish')
                                    ->placeholder('O\'qituvchi nomi, email...')
                                    ->suffixIcon('heroicon-o-magnifying-glass')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(fn () => $this->resetTable()),

                                Select::make('malaka_daraja')
                                    ->label('Malaka daraja')
                                    ->placeholder('Malaka darajasini tanlang')
                                    ->options([
                                        'oliy-toifa' => 'Oliy toifa',
                                        '1-toifa' => 'Birinchi toifa',
                                        '2-toifa' => 'Ikkinchi toifa',
                                        'mutaxasis' => 'Mutaxasis',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetTable()),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(function ($record) {
                        $name = $record->full_name ?? 'Teacher';
                        $encodedName = urlencode($name);

                        // Professional color palette
                        $professionalColors = [
                            '1E3A8A', // Navy Blue
                            '059669', // Emerald
                            'DC2626', // Red
                            'EA580C', // Orange
                            '7C3AED', // Violet
                            '0F766E', // Teal
                            'BE185D', // Rose
                            '374151', // Gray
                        ];

                        $colorIndex = abs(crc32($name)) % count($professionalColors);
                        $backgroundColor = $professionalColors[$colorIndex];

                        return "https://ui-avatars.com/api/?name={$encodedName}&color=FFFFFF&background={$backgroundColor}&bold=true&size=80&font-size=0.6&rounded=true";
                    })
                    ->extraAttributes([
                        'class' => 'shadow-sm border border-gray-200 dark:border-gray-700'
                    ]),

                TextColumn::make('full_name')
                    ->label('F.I.Sh')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Teacher $record): string => $record->user?->email ?? 'Email kiritilmagan')
                    ->weight('bold'),

                TextColumn::make('maktab.name')
                    ->label('Ta\'lim muassasasi')
                    ->searchable()
                    ->sortable()
                    ->description(function (Teacher $record): string {
                        $parts = [];
                        if ($record->maktab?->district?->name) {
                            $parts[] = $record->maktab->district->name;
                        }
                        if ($record->maktab?->district?->region?->name) {
                            $parts[] = $record->maktab->district->region->name;
                        }
                        return implode(', ', $parts);
                    })
                    ->wrap(),

                BadgeColumn::make('malaka_toifa_daraja')
                    ->label('Malaka daraja')
                    ->colors([
                        'success' => 'oliy-toifa',
                        'warning' => '1-toifa',
                        'info' => '2-toifa',
                        'gray' => 'mutaxasis',
                    ])
                    ->formatStateUsing(function (?string $state): string {
                        return match ($state) {
                            'oliy-toifa' => 'Oliy toifa',
                            '1-toifa' => 'Birinchi toifa',
                            '2-toifa' => 'Ikkinchi toifa',
                            'mutaxasis' => 'Mutaxasis',
                            default => 'Belgilanmagan',
                        };
                    }),

                TextColumn::make('subjects')
                    ->label('Fanlar')
                    ->getStateUsing(function (Teacher $record): string {
                        return $record->subjects->pluck('name')->join(', ') ?: 'Fanlar belgilanmagan';
                    })
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('malaka_toifa_daraja')
                    ->label('Malaka daraja')
                    ->options([
                        'oliy-toifa' => 'Oliy toifa',
                        '1-toifa' => 'Birinchi toifa',
                        '2-toifa' => 'Ikkinchi toifa',
                        'mutaxasis' => 'Mutaxasis',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Teacher $record): string => route('filament.app.resources.teachers.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('O\'qituvchilar topilmadi')
            ->emptyStateDescription('Hozircha hech qanday o\'qituvchi ma\'lumotlari mavjud emas.')
            ->emptyStateIcon('heroicon-o-users')
            ->bulkActions([
                // Bulk actions will be added here if needed
            ])
            ->defaultSort('full_name')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    protected function getTableQuery(): Builder
    {
        $query = Teacher::query()
            ->with(['maktab.district.region', 'user', 'subjects']);

        $formData = $this->form->getState();

        // Apply search filter
        if (!empty($formData['search'])) {
            $search = $formData['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply region filter
        if (!empty($formData['region_id'])) {
            $query->whereHas('maktab', function ($q) use ($formData) {
                $q->where('region_id', $formData['region_id']);
            });
        }

        // Apply district filter
        if (!empty($formData['district_id'])) {
            $query->whereHas('maktab', function ($q) use ($formData) {
                $q->where('district_id', $formData['district_id']);
            });
        }

        // Apply maktab filter
        if (!empty($formData['maktab_id'])) {
            $query->where('maktab_id', $formData['maktab_id']);
        }

        // Apply malaka daraja filter
        if (!empty($formData['malaka_daraja'])) {
            $query->where('malaka_toifa_daraja', $formData['malaka_daraja']);
        }

        return $query;
    }

    public function getStats(): array
    {
        $cacheKey = 'teacher_stats_' . md5(serialize($this->form->getState()));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $query = $this->getTableQuery();

            $total = $query->count();

            $malakaStats = $query->selectRaw('malaka_toifa_daraja, COUNT(*) as count')
                ->groupBy('malaka_toifa_daraja')
                ->pluck('count', 'malaka_toifa_daraja')
                ->toArray();

            return [
                'total' => $total,
                'oliy_toifa' => $malakaStats['oliy-toifa'] ?? 0,
                'birinchi_toifa' => $malakaStats['1-toifa'] ?? 0,
                'ikkinchi_toifa' => $malakaStats['2-toifa'] ?? 0,
                'mutaxasis' => $malakaStats['mutaxasis'] ?? 0,
                'belgilanmagan' => $total - array_sum($malakaStats),
            ];
        });
    }

    public function getSubheading(): ?string
    {
        return "Viloyat, tuman va maktablar kesimida pedagoglar statistikasi";
    }

    /**
     * Only allow superadmin (role_id = 3) to access this page
     */
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->role_id === 3;
    }
}
