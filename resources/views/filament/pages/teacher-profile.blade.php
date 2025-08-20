<x-filament-panels::page>
    <div class="space-y-6">
        <!-- O‘qituvchi Asosiy Ma’lumotlari -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    O‘qituvchi ma’lumotlari
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white-300">To‘liq IFSH</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $teacher->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $teacher->phone ?: 'Kiritilmagan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maktab</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $teacher->maktab->name ?? 'Biriktirilmagan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $teacher->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telegram ID</label>
                        <a href="https://t.me/{{ substr($teacher->telegram_id,1) }}" target="_blank"><p class="mt-1 text-sm text-blue-900 dark:text-blue-100">{{ $teacher->telegram_id??"Topilmadi" }}</p></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profil Formasi -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                    Profilingizni to‘ldiring
                </h3>
                <br>

                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="flex justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $this->getFormActions()[0] }}
                    </div>
                </form>
            </div>
        </div>

        <!-- Hozirgi hujjatlar (ixtiyoriy) -->
        @if($teacher->passport_photo_path || $teacher->diplom_path || $teacher->malaka_toifa_path || $teacher->milliy_sertifikat_path || $teacher->xalqaro_sertifikat_path || $teacher->malumotnoma_path)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Hozirgi hujjatlar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @if($teacher->passport_photo_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pasport rasmi</h4>
                                <img src="{{ $teacher->passport_photo_url }}" alt="Pasport rasmi" class="w-full h-32 object-cover rounded">
                            </div>
                        @endif

                        @if($teacher->diplom_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Diplom</h4>
                                <a href="{{ $teacher->diplom_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko‘rish
                                </a>
                            </div>
                        @endif

                        @if($teacher->malaka_toifa_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Malaka toifasi</h4>
                                <a href="{{ $teacher->malaka_toifa_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko‘rish
                                </a>
                            </div>
                        @endif

                        @if($teacher->milliy_sertifikat_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Milliy sertifikat</h4>
                                <a href="{{ $teacher->milliy_sertifikat_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko‘rish
                                </a>
                            </div>
                        @endif

                        @if($teacher->xalqaro_sertifikat_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Xalqaro sertifikat</h4>
                                <a href="{{ $teacher->xalqaro_sertifikat_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko‘rish
                                </a>
                            </div>
                        @endif

                        @if($teacher->malumotnoma_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ma’lumotnoma</h4>
                                <a href="{{ $teacher->malumotnoma_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko‘rish
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
