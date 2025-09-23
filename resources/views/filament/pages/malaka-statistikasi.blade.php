<x-filament-panels::page>
    <div class="space-y-6">
        <!-- First Row: Chart and Total Teachers -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart Widget - Takes up 2 columns -->
            <div class="lg:col-span-2">
                <!-- The chart widget will be rendered here by Filament -->
            </div>

            <!-- Total Teachers Card - Takes up 1 column -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jami o'qituvchilar soni</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->getStats()['total']) }}</p>
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <x-heroicon-o-users class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-blue-500 to-blue-600"></div>
            </div>
        </div>

        <!-- Second Row: Oliy toifa and 1-toifa -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Oliy Toifa Card -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Oliy toifa</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->getStats()['oliy_toifa']) }}</p>
                        @if($this->getStats()['total'] > 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ round(($this->getStats()['oliy_toifa'] / $this->getStats()['total']) * 100, 1) }}% barcha o'qituvchilardan</p>
                        @endif
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                        <x-heroicon-o-document-text class="h-8 w-8 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-green-500 to-green-600"></div>
            </div>

            <!-- 1-toifa Card -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Birinchi toifa</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->getStats()['birinchi_toifa']) }}</p>
                        @if($this->getStats()['total'] > 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ round(($this->getStats()['birinchi_toifa'] / $this->getStats()['total']) * 100, 1) }}% barcha o'qituvchilardan</p>
                        @endif
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                        <x-heroicon-o-document-text class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-purple-500 to-purple-600"></div>
            </div>
        </div>

        <!-- Third Row: 2-toifa and Mutaxasis -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 2-toifa Card -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ikkinchi toifa</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->getStats()['ikkinchi_toifa']) }}</p>
                        @if($this->getStats()['total'] > 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ round(($this->getStats()['ikkinchi_toifa'] / $this->getStats()['total']) * 100, 1) }}% barcha o'qituvchilardan</p>
                        @endif
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-yellow-100 dark:bg-yellow-900/30">
                        <x-heroicon-o-document-text class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
            </div>

            <!-- Mutaxasis Card -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Mutaxasis</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->getStats()['mutaxasis']) }}</p>
                        @if($this->getStats()['total'] > 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ round(($this->getStats()['mutaxasis'] / $this->getStats()['total']) * 100, 1) }}% barcha o'qituvchilardan</p>
                        @endif
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                        <x-heroicon-o-document-text class="h-8 w-8 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-gray-500 to-gray-600"></div>
            </div>
        </div>

        <!-- Filters Form -->
        <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            {{ $this->form }}
        </div>

        <!-- Teachers Table -->
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
