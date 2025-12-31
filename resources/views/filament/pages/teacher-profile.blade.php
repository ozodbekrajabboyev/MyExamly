@php
    use App\Jobs\FetchCertificateExpiry;
    use Illuminate\Support\Facades\Cache;
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Teacher Information -->
        <x-filament::card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center bg-primary-100 dark:bg-gradient-to-r dark:from-primary-500 dark:to-secondary-500">
                        <span class="text-xl font-bold text-primary-600 dark:text-white">
                            {{ strtoupper(substr($teacher->full_name, 0, 2)) }}
                        </span>
                    </div>
                </div>

                <div class="ml-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $teacher->full_name }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $teacher->maktab->name ?? 'Maktab biriktirilmagan' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $malaka_display = match($teacher->malaka_toifa_daraja) {
                        'oliy-toifa' => 'Oliy toifa',
                        '1-toifa' => 'Birinchi toifa',
                        '2-toifa' => 'Ikkinchi toifa',
                        'mutaxassis' => 'Mutaxassis',
                        default => 'Kiritilmagan'
                    };

                    $info_items = [
                        ['label' => 'To\'liq I.F.SH', 'value' => $teacher->full_name],
                        ['label' => 'Telefon', 'value' => $teacher->phone ?: 'Kiritilmagan'],
                        ['label' => 'Maktab', 'value' => $teacher->maktab->name ?? 'Biriktirilmagan'],
                        ['label' => 'Email', 'value' => $teacher->user->email, 'truncate' => true],
                        ['label' => 'Malaka toifa daraja', 'value' => $malaka_display],
                        ['label' => 'Telegram ID', 'value' => $teacher->telegram_id, 'is_link' => true],
                    ];
                @endphp

                @foreach ($info_items as $item)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ $item['label'] }}
                        </label>
                        @if(!empty($item['is_link']) && $item['value'])
                            <a href="https://t.me/{{ str_replace('@', '', $item['value']) }}" target="_blank"
                               class="text-sm font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                                {{ $item['value'] }}
                            </a>
                        @else
                            <p @class([
                                'text-sm font-semibold text-gray-800 dark:text-gray-200',
                                'truncate' => $item['truncate'] ?? false,
                                'text-gray-500 dark:text-gray-400' => $item['value'] === 'Kiritilmagan' || $item['value'] === 'Biriktirilmagan',
                            ])>
                                {{ $item['value'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::card>

        <!-- Profile Form -->
        <x-filament::card>
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-success-50 dark:bg-success-500/10 rounded-xl flex items-center justify-center">
                        <x-heroicon-o-pencil-square class="w-8 h-8 text-success-600 dark:text-success-400"/>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Profilingizni to'ldiring
                    </h3>
                    <p class="text-base text-gray-500 dark:text-gray-400">
                        Ma'lumotlaringizni yangilang va hujjatlaringizni yuklang
                    </p>
                </div>
            </div>
            <br>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="flex justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-filament::button type="submit" size="lg">
                        Saqlash
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>

        <!-- Uploaded Documents -->
        @php
            $documents = [
                ['path' => 'passport_photo_path', 'url' => 'passport_photo_url', 'title' => 'Pasport hujjati', 'icon_color' => 'danger', 'is_image' => true],
                ['path' => 'signature_path', 'url' => 'signature_url', 'title' => 'Shaxsiy Imzo', 'icon_color' => 'purple', 'is_image' => true],
                ['path' => 'diplom_path', 'url' => 'diplom_url', 'title' => 'Diplom', 'icon_color' => 'primary'],
                ['path' => 'malaka_toifa_path', 'url' => 'malaka_toifa_url', 'title' => 'Malaka toifasi', 'subtitle' => $malaka_display, 'icon_color' => 'success'],
                ['path' => 'milliy_sertifikat1_path', 'url' => 'milliy_sertifikat1_url', 'title' => '1-milliy sertifikat', 'icon_color' => 'warning'],
                ['path' => 'milliy_sertifikat2_path', 'url' => 'milliy_sertifikat2_url', 'title' => '2-milliy sertifikat', 'icon_color' => 'warning'],
                ['path' => 'xalqaro_sertifikat_path', 'url' => 'xalqaro_sertifikat_url', 'title' => 'Xalqaro sertifikat', 'icon_color' => 'info'],
                ['path' => 'ustama_sertifikat_path', 'url' => 'ustama_sertifikat_url', 'title' => 'Ustama sertifikat', 'icon_color' => 'pink'],
                ['path' => 'vazir_buyruq_path', 'url' => 'vazir_buyruq_url', 'title' => 'Vazir buyruqnomasi', 'icon_color' => 'danger'],
                ['path' => 'qoshimcha_ustama_path', 'url' => 'qoshimcha_ustama_url', 'title' => 'Qo\'shimcha ustama hujjati', 'icon_color' => 'orange'],
                ['path' => 'malumotnoma_path', 'url' => 'malumotnoma_url', 'title' => 'Ma\'lumotnoma (obyektivka)', 'icon_color' => 'teal'],
            ];
            $hasDocuments = collect($documents)->some(fn($doc) => !empty($teacher->{$doc['path']}));
        @endphp

        @if($teacher->passport_photo_path || $teacher->diplom_path || $teacher->malaka_toifa_path || $teacher->milliy_sertifikat1_path || $teacher->milliy_sertifikat2_path || $teacher->xalqaro_sertifikat_path || $teacher->ustama_sertifikat_path || $teacher->vazir_buyruq_path || $teacher->malumotnoma_path || $teacher->signature_path)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Hozirgi hujjatlar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        <!-- Pasport rasmi -->
                        @if($teacher->passport_photo_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Pasport hujjati</h4>
                                <a href="{{ $teacher->passport_photo_url }}" target="_blank" class="w-full h-32 object-cover rounded">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- Elektron Imzo -->
                        @if($teacher->signature_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Shaxsiy Imzo</h4>
                                <img src="{{ $teacher->signature_url }}" alt="Elektron Imzo" class="w-full h-32 object-cover rounded">
                            </div>
                        @endif

                        <!-- Diplom -->
                        @if($teacher->diplom_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Diplom</h4>
                                <a href="{{ $teacher->diplom_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- Malaka toifasi -->
                        @if($teacher->malaka_toifa_path && $teacher->isDocumentRequired('malaka_toifa_path'))
                            @php
                                $field = 'malaka_toifa_path';
                                $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";
                                $expire_date = Cache::get($cacheKey);
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Malaka toifasi ({{ $teacher->malaka_toifa_daraja ?? 'Daraja kiritilmagan' }})</h4>
                                @if($expire_date)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Amal qilish muddati: {{ $expire_date }}</p>
                                @endif
                                <a href="{{ $teacher->malaka_toifa_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- 1-Milliy sertifikat -->
                        @php
                            $field = 'milliy_sertifikat1_path';
                            $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";
                            $expire_date = Cache::get($cacheKey);
                        @endphp
                        @if($teacher->milliy_sertifikat1_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">1-milliy sertifikat</h4>
                                @if($expire_date)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Amal qilish muddati: {{ $expire_date }}</p>
                                @endif
                                <a href="{{ $teacher->milliy_sertifikat1_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- 2-Milliy sertifikat -->
                        @php
                            $field = 'milliy_sertifikat2_path';
                            $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";
                            $expire_date = Cache::get($cacheKey);
                        @endphp
                        @if($teacher->milliy_sertifikat2_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">2-milliy sertifikat</h4>
                                @if($expire_date)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Amal qilish muddati: {{ $expire_date }}</p>
                                @endif
                                <a href="{{ $teacher->milliy_sertifikat2_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- Xalqaro sertifikat -->
                        @if($teacher->xalqaro_sertifikat_path)
                            @php
                                $field = 'xalqaro_sertifikat_path';
                                $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";
                                $expire_date = Cache::get($cacheKey);
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Xalqaro sertifikat</h4>
                                @if($expire_date)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Amal qilish muddati: {{ $expire_date }}</p>
                                @endif
                                <a href="{{ $teacher->xalqaro_sertifikat_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- Ustama sertifikat -->
                        @php
                            $field = 'ustama_sertifikat_path';
                            $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";
                            $expire_date = Cache::get($cacheKey);
                        @endphp
                        @if($teacher->ustama_sertifikat_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Ustama sertifikat</h4>
                                @if($expire_date)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Amal qilish muddati: {{ $expire_date }}</p>
                                @endif
                                <a href="{{ $teacher->ustama_sertifikat_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- Vazir buyruq -->
                        @if($teacher->vazir_buyruq_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Vazir buyruqnomasi</h4>
                                <a href="{{ $teacher->vazir_buyruq_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- Qo'shimcha ustama hujjati -->
                        @if($teacher->qoshimcha_ustama_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Qo'shimcha ustama hujjati</h4>
                                <a href="{{ $teacher->qoshimcha_ustama_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                        <!-- Ma'lumotnoma -->
                        @if($teacher->malumotnoma_path)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Ma'lumotnoma(obyektivka)</h4>
                                <a href="{{ $teacher->malumotnoma_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Hujjatni ko'rish
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
