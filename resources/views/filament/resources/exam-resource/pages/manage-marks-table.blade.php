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

        /* =================================== */
        /* DESKTOP-SAFE TABLE CONTAINER        */
        /* Basic overflow for all screen sizes */
        /* =================================== */
        .fi-ta-table-container {
            overflow-x: auto !important;
        }

        /* =================================== */
        /* MOBILE AND TABLET OPTIMIZATIONS    */
        /* Only applies to screens ≤ 1024px   */
        /* Desktop (>1024px) keeps original   */
        /* =================================== */

        /* Tablet and mobile enhanced scrolling */
        @media (max-width: 1024px) {
            .fi-ta-table-container {
                -webkit-overflow-scrolling: touch !important; /* iOS smooth scrolling */
                position: relative;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f7fafc;
            }

            /* WebKit scrollbar styling for mobile/tablet */
            .fi-ta-table-container::-webkit-scrollbar {
                height: 8px;
            }

            .fi-ta-table-container::-webkit-scrollbar-track {
                background: #f7fafc;
                border-radius: 4px;
            }

            .fi-ta-table-container::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 4px;
            }

            .fi-ta-table-container::-webkit-scrollbar-thumb:hover {
                background: #a0aec0;
            }
        }

        /* Tablet specific (769px to 1024px) */
        @media (max-width: 1024px) and (min-width: 769px) {
            /* Subtle scroll indicators for tablets */
            .fi-ta-table-container::before,
            .fi-ta-table-container::after {
                content: '';
                position: absolute;
                top: 0;
                bottom: 0;
                width: 15px;
                z-index: 15;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            .fi-ta-table-container::before {
                left: 200px;
                background: linear-gradient(to right, transparent, rgba(0, 0, 0, 0.05));
                opacity: 0;
            }

            .fi-ta-table-container::after {
                right: 100px;
                background: linear-gradient(to left, transparent, rgba(0, 0, 0, 0.05));
                opacity: 1;
            }

            .fi-ta-table-container.scrolled-left::before {
                opacity: 1;
            }

            .fi-ta-table-container.scrolled-right::after {
                opacity: 0;
            }
        }
        /* Mobile specific optimizations (768px and below) */
        @media (max-width: 768px) {
            /* Mobile scroll indicators */
            .fi-ta-table-container::before,
            .fi-ta-table-container::after {
                content: '';
                position: absolute;
                top: 0;
                bottom: 0;
                width: 20px;
                z-index: 15;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            .fi-ta-table-container::before {
                left: 150px; /* Position after sticky column on mobile */
                background: linear-gradient(to right, transparent, rgba(0, 0, 0, 0.1));
                opacity: 0;
            }

            .fi-ta-table-container::after {
                right: 80px; /* Position before total column on mobile */
                background: linear-gradient(to left, transparent, rgba(0, 0, 0, 0.1));
                opacity: 1;
            }

            .fi-ta-table-container.scrolled-left::before {
                opacity: 1;
            }

            .fi-ta-table-container.scrolled-right::after {
                opacity: 0;
            }

            /* Mobile responsive column sizing */
            .student-name-column,
            .fi-ta-table .fi-ta-cell:first-child,
            .fi-ta-table .fi-ta-header-cell:first-child {
                min-width: 150px !important;
                max-width: 180px !important;
            }

            /* Mobile problem columns */
            .problem-column,
            .fi-ta-table .mark-input {
                width: 80px !important;
                min-width: 80px !important;
                font-size: 14px !important;
                padding: 8px 4px !important;
            }

            /* Mobile total column */
            .fi-ta-table .total-column {
                min-width: 80px !important;
                max-width: 90px !important;
                font-size: 14px !important;
            }

            /* Improve touch targets for mobile */
            .fi-ta-table input[type="number"] {
                min-height: 44px !important; /* iOS recommended touch target */
                padding: 12px 8px !important;
            }

            /* Better table header on mobile */
            .fi-ta-table .fi-ta-header-cell {
                font-size: 12px !important;
                line-height: 1.2 !important;
                padding: 8px 4px !important;
            }

            /* Mobile table improvements */
            .fi-ta-table {
                font-size: 14px !important;
            }

            .fi-ta-header {
                padding: 12px 16px !important;
            }

            .fi-ta-header-heading {
                font-size: 18px !important;
            }

            .fi-ta-header-description {
                font-size: 13px !important;
                margin-top: 4px !important;
            }

            /* Mobile action buttons */
            .fi-ta-header-actions {
                gap: 8px !important;
            }

            .fi-ta-header-actions .fi-btn {
                padding: 8px 12px !important;
                font-size: 13px !important;
            }

            /* Mobile table borders */
            .fi-ta-table .fi-ta-cell,
            .fi-ta-table .fi-ta-header-cell {
                border-width: 1px !important;
                border-style: solid !important;
            }

            /* Mobile sticky column shadows */
            .student-name-column,
            .fi-ta-table .fi-ta-cell:first-child,
            .fi-ta-table .fi-ta-header-cell:first-child {
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1) !important;
            }

            .dark .student-name-column,
            .dark .fi-ta-table .fi-ta-cell:first-child,
            .dark .fi-ta-table .fi-ta-header-cell:first-child {
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3) !important;
            }

            /* Mobile total column shadow */
            .fi-ta-table .total-column {
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1) !important;
            }

            .dark .fi-ta-table .total-column {
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3) !important;
            }
        }

        /* Very small screens (phones in portrait) */
        @media (max-width: 480px) {
            .student-name-column,
            .fi-ta-table .fi-ta-cell:first-child,
            .fi-ta-table .fi-ta-header-cell:first-child {
                min-width: 120px !important;
                max-width: 140px !important;
                font-size: 12px !important;
            }

            .problem-column,
            .fi-ta-table .mark-input {
                width: 70px !important;
                min-width: 70px !important;
                font-size: 13px !important;
            }

            .fi-ta-table .total-column {
                min-width: 70px !important;
                max-width: 80px !important;
                font-size: 13px !important;
            }
        }

        /* Mobile table wrapper improvements - only on mobile/tablet */
        @media (max-width: 1024px) {
            .mobile-optimized-table {
                position: relative;
            }

            .mobile-optimized-table .fi-ta-content {
                padding: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .mobile-optimized-table .fi-ta-table-container {
                margin: 0 -16px !important;
                padding: 0 16px !important;
            }
        }

        /* =================================== */
        /* DESKTOP PRESERVATION                */
        /* Remove mobile shadows on desktop   */
        /* =================================== */
        @media (min-width: 1025px) {
            /* Remove any mobile-specific shadows or effects on desktop */
            .fi-ta-table .fi-ta-cell:first-child,
            .fi-ta-table .fi-ta-header-cell:first-child {
                box-shadow: none !important;
            }

            .fi-ta-table .total-column {
                box-shadow: none !important;
            }

            .dark .fi-ta-table .fi-ta-cell:first-child,
            .dark .fi-ta-table .fi-ta-header-cell:first-child {
                box-shadow: none !important;
            }

            .dark .fi-ta-table .total-column {
                box-shadow: none !important;
            }
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

        /* =================================== */
        /* UNIVERSAL STYLES (ALL SCREEN SIZES) */
        /* These apply to both desktop & mobile */
        /* =================================== */

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
            // Get valid problem IDs from the exam structure (passed from PHP)
            const validProblemIds = @json(collect($this->problems ?? [])->pluck('id')->toArray());
            console.log('Valid problem IDs for exam:', validProblemIds);

            // Debounce function to prevent excessive calculations
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Consistent calculation function (matches PHP logic)
            function calculateTotal(inputs) {
                let total = 0;
                inputs.forEach(input => {
                    const problemId = parseInt(input.getAttribute('data-problem-id'));
                    // Only include marks for problems defined in the exam structure
                    if (validProblemIds.includes(problemId)) {
                        const value = parseFloat(input.value) || 0;
                        total += value;
                    }
                });
                // Use parseFloat with toFixed(1) to match PHP number_format($total, 1)
                return parseFloat(total.toFixed(1));
            }

            // Function to calculate and update total for a specific student row
            window.updateTotal = debounce(function(inputElement) {
                const row = inputElement.closest('tr');
                if (!row) return;

                // Get ALL mark inputs in this row (including potentially orphaned ones)
                const markInputs = Array.from(row.querySelectorAll('input[type="number"][data-problem-id]'));

                // Use consistent calculation logic (which filters by valid problem IDs)
                const total = calculateTotal(markInputs);

                // Find and update the total column in this row
                const totalColumn = row.querySelector('.total-column');
                if (totalColumn) {
                    // Format consistently with PHP (1 decimal place)
                    totalColumn.textContent = total.toFixed(1);

                    // Add visual feedback
                    totalColumn.style.animation = 'none';
                    totalColumn.offsetHeight; // Trigger reflow
                    totalColumn.style.animation = 'pulse 0.5s';
                }

                // Optional: Validate against expected total (could be enhanced with AJAX call)
                console.log(`Student total calculated: ${total} from ${markInputs.filter(input => validProblemIds.includes(parseInt(input.getAttribute('data-problem-id')))).length} valid inputs`);
            }, 300); // Increased debounce to 300ms to allow for Filament updates

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

            // Enhanced observer for dynamic content
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            const inputs = node.querySelectorAll ? node.querySelectorAll('input[type="number"][data-problem-id]') : [];
                            inputs.forEach(input => {
                                if (!input.hasAttribute('data-total-listener')) {
                                    // Add input event listener for real-time total updates
                                    input.addEventListener('input', function() {
                                        updateTotal(this);
                                    });

                                    // Add blur event listener for when field loses focus (after Filament save)
                                    input.addEventListener('blur', function() {
                                        const currentValue = this.value;
                                        setTimeout(() => {
                                            // Ensure the input value is what we expect
                                            if (this.value !== currentValue) {
                                                console.log(`Input value changed from ${currentValue} to ${this.value}`);
                                            }
                                            updateTotal(this);
                                        }, 500); // Delay to allow Filament to complete its update cycle
                                    });

                                    // Add keydown event listener for Tab key handling
                                    input.addEventListener('keydown', function(e) {
                                        if (e.key === 'Tab') {
                                            // Save current value before tab
                                            const currentValue = this.value;
                                            setTimeout(() => {
                                                // After tab, make sure the value is preserved
                                                if (this.value !== currentValue && currentValue !== '') {
                                                    console.log(`Restoring value ${currentValue} after Tab key`);
                                                    this.value = currentValue;
                                                    // Trigger input event to ensure it's saved
                                                    this.dispatchEvent(new Event('input', { bubbles: true }));
                                                }
                                                updateTotal(this);
                                            }, 100);
                                        }
                                    });

                                    // Add change event listener for final validation
                                    input.addEventListener('change', function() {
                                        setTimeout(() => {
                                            updateTotal(this);
                                        }, 300);
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

            // Listen for Livewire/Alpine events when marks are updated
            document.addEventListener('mark-updated', function(event) {
                console.log('Mark updated event received:', event.detail);
                setTimeout(() => {
                    updateAllTotals();
                    // Force refresh of the specific input field to show the saved value
                    const studentId = event.detail.studentId;
                    const problemId = event.detail.problemId;
                    const newMark = event.detail.newMark;

                    // Find and update the specific input
                    const input = document.querySelector(`input[data-problem-id="${problemId}"]`);
                    if (input) {
                        const row = input.closest('tr');
                        if (row) {
                            const allInputsInRow = row.querySelectorAll('input[data-problem-id]');
                            // Find the correct input for this student
                            allInputsInRow.forEach(inp => {
                                if (inp.getAttribute('data-problem-id') == problemId) {
                                    inp.value = newMark;
                                    updateTotal(inp);
                                }
                            });
                        }
                    }
                }, 100);
            });

            // Also listen for Livewire component updates
            document.addEventListener('livewire:updated', function() {
                console.log('Livewire component updated');
                setTimeout(() => {
                    updateAllTotals();
                }, 200);
            });

            // Listen for Alpine.js data updates (Filament uses Alpine)
            document.addEventListener('alpine:updated', function() {
                console.log('Alpine component updated');
                setTimeout(() => {
                    updateAllTotals();
                }, 150);
            });

            // Initial calculation for existing elements (with delay for DOM readiness)
            setTimeout(() => {
                // Add event listeners to existing inputs
                const existingInputs = document.querySelectorAll('input[type="number"][data-problem-id]');
                existingInputs.forEach(input => {
                    if (!input.hasAttribute('data-total-listener')) {
                        // Add input event listener for real-time total updates
                        input.addEventListener('input', function() {
                            updateTotal(this);
                        });

                        // Add blur event listener for when field loses focus (after Filament save)
                        input.addEventListener('blur', function() {
                            const currentValue = this.value;
                            setTimeout(() => {
                                // Ensure the input value is what we expect
                                if (this.value !== currentValue) {
                                    console.log(`Input value changed from ${currentValue} to ${this.value}`);
                                }
                                updateTotal(this);
                            }, 500); // Delay to allow Filament to complete its update cycle
                        });

                        // Add keydown event listener for Tab key handling
                        input.addEventListener('keydown', function(e) {
                            if (e.key === 'Tab') {
                                // Save current value before tab
                                const currentValue = this.value;
                                setTimeout(() => {
                                    // After tab, make sure the value is preserved
                                    if (this.value !== currentValue && currentValue !== '') {
                                        console.log(`Restoring value ${currentValue} after Tab key`);
                                        this.value = currentValue;
                                        // Trigger input event to ensure it's saved
                                        this.dispatchEvent(new Event('input', { bubbles: true }));
                                    }
                                    updateTotal(this);
                                }, 100);
                            }
                        });

                        // Add change event listener for final validation
                        input.addEventListener('change', function() {
                            setTimeout(() => {
                                updateTotal(this);
                            }, 300);
                        });

                        input.setAttribute('data-total-listener', 'true');
                    }
                });

                updateAllTotals();
                initializeMobileScrollIndicators();
            }, 500);

            // Mobile scroll indicators functionality
            function initializeMobileScrollIndicators() {
                const tableContainer = document.querySelector('.fi-ta-table-container');
                if (!tableContainer) return;

                function updateScrollIndicators() {
                    const scrollLeft = tableContainer.scrollLeft;
                    const scrollWidth = tableContainer.scrollWidth;
                    const clientWidth = tableContainer.clientWidth;
                    const maxScroll = scrollWidth - clientWidth;

                    // Update scroll indicator classes
                    if (scrollLeft > 20) {
                        tableContainer.classList.add('scrolled-left');
                    } else {
                        tableContainer.classList.remove('scrolled-left');
                    }

                    if (scrollLeft < maxScroll - 20) {
                        tableContainer.classList.add('can-scroll-right');
                    } else {
                        tableContainer.classList.add('scrolled-right');
                        tableContainer.classList.remove('can-scroll-right');
                    }
                }

                // Add scroll event listener with throttling
                let scrollTimeout;
                tableContainer.addEventListener('scroll', function() {
                    if (scrollTimeout) clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(updateScrollIndicators, 10);
                });

                // Initialize indicators
                updateScrollIndicators();

                // Re-check on window resize
                window.addEventListener('resize', updateScrollIndicators);
            }

            // Add touch-friendly interaction for mobile devices only
            function initializeMobileTouch() {
                // Only apply on touch devices or small screens
                if (!('ontouchstart' in window) && window.innerWidth > 768) {
                    return; // Skip on desktop without touch
                }

                const markInputs = document.querySelectorAll('.mark-input');

                markInputs.forEach(input => {
                    // Prevent zoom on iOS when focusing input
                    input.addEventListener('touchstart', function() {
                        // Temporarily increase font size to prevent zoom
                        this.style.fontSize = '16px';
                    });

                    input.addEventListener('blur', function() {
                        // Reset font size
                        this.style.fontSize = '';
                    });

                    // Better mobile keyboard handling
                    input.addEventListener('focus', function() {
                        // Scroll the input into view on mobile
                        setTimeout(() => {
                            this.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center',
                                inline: 'center'
                            });
                        }, 300);
                    });
                });
            }

            // Initialize mobile enhancements only on mobile/tablet devices
            setTimeout(() => {
                // Check if we're on a mobile/tablet device (screen width <= 1024px)
                if (window.innerWidth <= 1024) {
                    initializeMobileTouch();
                    initializeMobileScrollIndicators();
                }

                // Re-check on window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth <= 1024) {
                        initializeMobileScrollIndicators();
                    }
                });
            }, 1000);
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
                <!-- Mobile and Tablet scroll hint (hidden on desktop) -->
                <div class="block lg:hidden bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-600 px-4 py-3">
                    <div class="flex items-center justify-between text-sm text-blue-800 dark:text-blue-200">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18m-2 4l4-4m0 0l-4-4"></path>
                            </svg>
                            <span>Chapga-o'nga surib ko'ring</span>
                        </div>
                        <div class="text-xs opacity-75">
                            {{ count($this->problems) }} ta topshiriq
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto relative">
                    <!-- Loading overlay for mobile -->
                    <div id="mobile-loading" class="hidden absolute inset-0 bg-white/80 dark:bg-gray-800/80 flex items-center justify-center z-20 md:hidden">
                        <div class="flex items-center space-x-2 text-gray-600 dark:text-gray-300">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">Yuklanmoqda...</span>
                        </div>
                    </div>

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
