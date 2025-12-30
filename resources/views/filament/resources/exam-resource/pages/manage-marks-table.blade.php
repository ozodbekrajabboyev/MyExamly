<x-filament-panels::page>
    <style>
        /* Custom CSS for sticky first column */
        .fi-ta-table .fi-ta-cell:first-child,
        .fi-ta-table .fi-ta-header-cell:first-child {
            position: sticky !important;
            left: 0 !important;
            z-index: 10 !important;
            border-right: 1px solid #e5e7eb !important;
        }

        /* Light mode sticky column */
        .fi-ta-table .fi-ta-cell:first-child,
        .fi-ta-table .fi-ta-header-cell:first-child {
            background-color: inherit !important;
            color: inherit !important;
            border-right-color: #e5e7eb !important;
        }

        .fi-ta-table .fi-ta-header-cell:first-child {
            background-color: inherit !important;
        }

        .fi-ta-table .fi-ta-row:hover .fi-ta-cell:first-child {
            background-color: inherit !important;
        }

        /* Dark mode sticky column - match the table background */
        .dark .fi-ta-table .fi-ta-cell:first-child,
        .dark .fi-ta-table .fi-ta-header-cell:first-child {
            background-color: inherit !important;
            color: inherit !important;
            border-right-color: #4b5563 !important;
        }

        .dark .fi-ta-table .fi-ta-header-cell:first-child {
            background-color: inherit !important;
        }

        .dark .fi-ta-table .fi-ta-row:hover .fi-ta-cell:first-child {
            background-color: inherit !important;
        }

        .dark .fi-ta-table .fi-ta-row:nth-child(even) .fi-ta-cell:first-child {
            background-color: inherit !important;
        }

        /* Dark mode for ALL table elements - make everything dark */
        .dark .fi-ta-table,
        .dark .fi-ta-table * {
            background-color: rgb(31 41 55) !important;
        }

        .dark .fi-ta-header-cell,
        .dark .fi-ta-header-cell * {
            background-color: rgb(17 24 39) !important;
            color: #f3f4f6 !important;
            border-color: #4b5563 !important;
        }

        .dark .fi-ta-cell,
        .dark .fi-ta-cell * {
            background-color: rgb(31 41 55) !important;
            color: #d1d5db !important;
            border-color: #4b5563 !important;
        }

        .dark .fi-ta-row:hover .fi-ta-cell,
        .dark .fi-ta-row:hover .fi-ta-cell * {
            background-color: rgb(55 65 81) !important;
        }

        .dark .fi-ta-row:nth-child(even) .fi-ta-cell,
        .dark .fi-ta-row:nth-child(even) .fi-ta-cell * {
            background-color: rgb(38 47 61) !important;
        }

        /* Input fields in dark mode - force dark backgrounds */
        .dark .fi-input,
        .dark input[type="number"],
        .dark input[type="text"] {
            background-color: rgb(55 65 81) !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }

        .dark .fi-input:focus,
        .dark input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 1px #3b82f6 !important;
            background-color: rgb(55 65 81) !important;
        }

        /* Force dark mode on Filament table wrapper */
        .dark [data-filament-table],
        .dark [data-filament-table] * {
            background-color: rgb(31 41 55) !important;
        }

        .dark .fi-ta-content,
        .dark .fi-ta-content * {
            background-color: rgb(31 41 55) !important;
        }

        /* Table heading and description */
        .dark .fi-ta-header-heading {
            color: #f3f4f6 !important;
        }

        .dark .fi-ta-header-description {
            color: #9ca3af !important;
        }

        .fi-ta-table table {
            border-collapse: separate !important;
        }

        /* Ensure table container allows horizontal scroll */
        .fi-ta-table-container {
            overflow-x: auto !important;
        }

        /* Remove any box shadows that might cause dark backgrounds */
        .dark .fi-ta-table .fi-ta-cell:first-child,
        .dark .fi-ta-table .fi-ta-header-cell:first-child {
            box-shadow: none !important;
        }

        /* Force all table elements to be dark */
        .dark table,
        .dark table *,
        .dark tbody,
        .dark tbody *,
        .dark thead,
        .dark thead *,
        .dark tr,
        .dark tr *,
        .dark td,
        .dark td *,
        .dark th,
        .dark th * {
            background-color: rgb(31 41 55) !important;
            color: #d1d5db !important;
        }

        .dark thead th,
        .dark thead th * {
            background-color: rgb(17 24 39) !important;
            color: #f3f4f6 !important;
        }
        /* Total column styling */
        .total-column {
            background-color: #eff6ff !important;
            border-left: 2px solid #3b82f6 !important;
            font-weight: bold !important;
        }

        .dark .total-column {
            background-color: rgb(30 58 138 / 0.2) !important;
            border-left-color: #60a5fa !important;
            color: #93c5fd !important;
        }

        /* Total column sticky positioning */
        .fi-ta-table .total-column {
            position: sticky !important;
            right: 0 !important;
            z-index: 5 !important;
        }

        /* Mark input highlighting on focus */
        .mark-input:focus {
            background-color: #fef3c7 !important;
            border-color: #f59e0b !important;
        }

        .dark .mark-input:focus {
            background-color: rgb(120 53 15 / 0.2) !important;
            border-color: #fbbf24 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to calculate and update total for a specific student row
            window.updateTotal = function(inputElement) {
                const row = inputElement.closest('tr');
                if (!row) return;

                // Find all mark inputs in this row
                const markInputs = row.querySelectorAll('input[type="number"][data-problem-id]');
                let total = 0;

                markInputs.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    total += value;
                });

                // Find and update the total column in this row
                const totalColumn = row.querySelector('.total-column');
                if (totalColumn) {
                    totalColumn.textContent = total.toFixed(1);

                    // Add visual feedback
                    totalColumn.style.animation = 'none';
                    totalColumn.offsetHeight; // Trigger reflow
                    totalColumn.style.animation = 'pulse 0.5s';
                }
            };

            // Function to update all totals (in case of bulk changes)
            window.updateAllTotals = function() {
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const firstInput = row.querySelector('input[type="number"][data-problem-id]');
                    if (firstInput) {
                        updateTotal(firstInput);
                    }
                });
            };

            // Add event listeners to existing inputs (for dynamically loaded content)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            const inputs = node.querySelectorAll ? node.querySelectorAll('input[type="number"][data-problem-id]') : [];
                            inputs.forEach(input => {
                                if (!input.hasAttribute('data-total-listener')) {
                                    input.addEventListener('input', function() {
                                        updateTotal(this);
                                    });
                                    input.setAttribute('data-total-listener', 'true');
                                }
                            });
                        }
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            // Initial calculation for existing elements
            setTimeout(() => {
                updateAllTotals();
            }, 500);
        });

        // Add CSS animation for pulse effect
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>

    <div class="space-y-6">
        <!-- Exam Information Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Imtihon ma'lumotlari</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fan</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->exam->subject->name }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Sinf</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->exam->sinf->name }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Imtihon</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->exam->serial_number }} - {{ $this->exam->type }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                    <x-filament::badge
                                        :color="match($this->exam->status) {
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'gray'
                                        }"
                                        size="md"
                                        class="font-medium"
                                    >
                                        {{ match($this->exam->status) {
                                            'pending' => 'Jarayonda',
                                            'approved' => 'Tasdiqlangan',
                                            'rejected' => 'Rad etilgan',
                                            default => $this->exam->status
                                        } }}
                                    </x-filament::badge>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        @if($this->students->count() > 0 && count($this->problems) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="min-w-full">
                        {{ $this->table }}
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-600 rounded-lg p-6 text-center">
                <div class="text-yellow-800 dark:text-yellow-200">
                    @if($this->students->count() === 0)
                        <x-heroicon-o-users class="mx-auto h-8 w-8 text-yellow-600 dark:text-yellow-400 mb-3" />
                        <h3 class="text-lg font-medium mb-2">Sinfda o'quvchilar yo'q</h3>
                        <p>Baholarni kiritish uchun avval sinfga o'quvchilar qo'shing.</p>
                    @elseif(count($this->problems) === 0)
                        <x-heroicon-o-clipboard-document-list class="mx-auto h-8 w-8 text-yellow-600 dark:text-yellow-400 mb-3" />
                        <h3 class="text-lg font-medium mb-2">Imtihonda topshiriqlar yo'q</h3>
                        <p>Baholarni kiritish uchun avval imtihonga topshiriqlar qo'shing.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
