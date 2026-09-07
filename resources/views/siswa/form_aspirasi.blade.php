@extends('layouts.app')

@section('title', 'Buat Aspirasi Baru')
@section('page-title', 'Buat Aspirasi Baru')
@section('page-subtitle', 'Sampaikan keluhan atau masukan terkait sarana dan prasarana sekolah')

@section('content')
<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- ── Main Form ─────────────────────────────────────────────── --}}
    <div class="xl:col-span-2">
        <form method="POST" action="{{ route('siswa.complaints.store') }}"
              enctype="multipart/form-data"
              x-data="aspirasiform()"
              @submit.prevent="submitForm"
              novalidate>
            @csrf

            <x-card>
                <x-slot:title>Detail Aspirasi</x-slot:title>

                <div class="space-y-5">

                    {{-- Judul --}}
                    <x-form-input name="title" label="Judul Aspirasi" :required="true" hint="Buat judul yang singkat dan deskriptif, maks. 200 karakter.">
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title') }}"
                               maxlength="200"
                               placeholder="cth: Proyektor Ruang XII RPL 1 Tidak Berfungsi"
                               x-model="title"
                               class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('title') border-red-400 bg-red-50 @enderror"
                               required>
                        <div class="mt-1 flex justify-end">
                            <span class="text-xs text-slate-400" x-text="title.length + '/200'"></span>
                        </div>
                    </x-form-input>

                    {{-- Kategori & Prioritas --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-form-input name="category_id" label="Kategori" :required="true">
                            <select name="category_id" id="category_id"
                                    class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('category_id') border-red-400 bg-red-50 @enderror"
                                    required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form-input>

                        <x-form-input name="priority" label="Tingkat Prioritas" :required="true">
                            <select name="priority" id="priority"
                                    class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('priority') border-red-400 bg-red-50 @enderror"
                                    required>
                                <option value="">-- Pilih Prioritas --</option>
                                <option value="rendah"  {{ old('priority') === 'rendah'  ? 'selected' : '' }}>↓ Rendah</option>
                                <option value="sedang"  {{ old('priority') === 'sedang'  ? 'selected' : '' }}>→ Sedang</option>
                                <option value="tinggi"  {{ old('priority') === 'tinggi'  ? 'selected' : '' }}>↑ Tinggi</option>
                                <option value="kritis"  {{ old('priority') === 'kritis'  ? 'selected' : '' }}>⚡ Kritis</option>
                            </select>
                        </x-form-input>
                    </div>

                    {{-- Lokasi --}}
                    <x-form-input name="location" label="Lokasi" :required="true" hint="cth: Ruang XII RPL 1, Gedung B Lantai 2">
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   name="location"
                                   id="location"
                                   value="{{ old('location') }}"
                                   maxlength="200"
                                   placeholder="Sebutkan lokasi secara spesifik"
                                   class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('location') border-red-400 bg-red-50 @enderror"
                                   required>
                        </div>
                    </x-form-input>

                    {{-- Deskripsi --}}
                    <x-form-input name="description" label="Deskripsi Aspirasi" :required="true" hint="Minimal 20 karakter. Jelaskan masalah secara rinci agar dapat ditangani dengan tepat.">
                        <textarea name="description"
                                  id="description"
                                  rows="5"
                                  maxlength="2000"
                                  placeholder="Jelaskan kondisi masalah, sudah berapa lama terjadi, dampak yang dirasakan, dan informasi relevan lainnya..."
                                  x-model="description"
                                  class="w-full resize-none rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 leading-relaxed transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('description') border-red-400 bg-red-50 @enderror"
                                  required>{{ old('description') }}</textarea>
                        <div class="mt-1 flex items-center justify-between">
                            <span class="text-xs" :class="description.length < 20 ? 'text-amber-500' : 'text-slate-400'">
                                <span x-show="description.length < 20">Minimal 20 karakter</span>
                            </span>
                            <span class="text-xs text-slate-400" x-text="description.length + '/2000'"></span>
                        </div>
                    </x-form-input>

                    {{-- Upload Foto --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Foto Bukti
                            <span class="ml-1 text-xs font-normal text-slate-400">(Opsional, maks. 5 foto, masing-masing 3 MB)</span>
                        </label>

                        <div x-data="imageUpload()"
                             class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 transition-all"
                             :class="isDragging ? 'border-primary-400 bg-primary-50' : 'hover:border-slate-300'"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleDrop($event)">

                            {{-- Drop zone --}}
                            <div x-show="previews.length === 0" class="px-6 py-10 text-center">
                                <label for="images" class="cursor-pointer">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-slate-200 text-slate-400 mb-3">
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">Klik untuk memilih foto</p>
                                    <p class="text-xs text-slate-400 mt-1">atau seret dan lepas di sini</p>
                                    <p class="text-xs text-slate-300 mt-1">JPG, JPEG, PNG, WebP</p>
                                </label>
                            </div>

                            {{-- Preview grid --}}
                            <div x-show="previews.length > 0" class="p-4">
                                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                                    <template x-for="(preview, index) in previews" :key="index">
                                        <div class="group relative aspect-square overflow-hidden rounded-xl bg-slate-200">
                                            <img :src="preview" class="h-full w-full object-cover">
                                            <button type="button"
                                                    @click="removeImage(index)"
                                                    class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow-md transition-opacity group-hover:opacity-100">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    {{-- Add more --}}
                                    <template x-if="previews.length < 5">
                                        <label for="images" class="flex aspect-square cursor-pointer items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-white text-slate-400 hover:border-primary-300 hover:text-primary-400 transition-colors">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        </label>
                                    </template>
                                </div>
                                <p class="mt-2 text-xs text-slate-400" x-text="previews.length + '/5 foto dipilih'"></p>
                            </div>

                            <input type="file" name="images[]" id="images"
                                   multiple accept="image/*" class="sr-only"
                                   @change="handleFiles($event.target.files)">
                        </div>
                        @error('images.*')
                            <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Action buttons --}}
            <div class="mt-4 flex items-center justify-end gap-3">
                <a href="{{ route('siswa.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm shadow-primary-200 hover:bg-primary-700 disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                    <template x-if="submitting">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </template>
                    <template x-if="!submitting">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </template>
                    <span x-text="submitting ? 'Mengirim...' : 'Kirim Aspirasi'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- ── Info Panel ───────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Tips --}}
        <x-card title="💡 Tips Membuat Aspirasi">
            <ul class="space-y-4">
                @foreach([
                    ['bg' => 'bg-blue-50 text-blue-600',   'icon' => 'eye',          'head' => 'Lokasi Spesifik',   'body' => 'Sebutkan nama ruangan, gedung, dan lantai secara lengkap.'],
                    ['bg' => 'bg-purple-50 text-purple-600','icon' => 'chart-bar',    'head' => 'Deskripsi Detail',  'body' => 'Jelaskan kondisi masalah, sudah berapa lama, dan dampaknya.'],
                    ['bg' => 'bg-emerald-50 text-emerald-600','icon' => 'chart-bar',  'head' => 'Sertakan Foto',    'body' => 'Foto bukti membantu admin memahami situasi lebih cepat.'],
                    ['bg' => 'bg-amber-50 text-amber-600',  'icon' => 'arrow-path',   'head' => 'Pantau Status',    'body' => 'Cek riwayat aspirasi untuk memantau perkembangan penanganan.'],
                ] as $tip)
                    <li class="flex gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl {{ $tip['bg'] }}">
                            @include('layouts.icons', ['icon' => $tip['icon'], 'class' => 'h-4 w-4'])
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ $tip['head'] }}</p>
                            <p class="mt-0.5 text-xs text-slate-500 leading-relaxed">{{ $tip['body'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-card>

        {{-- Kategori list --}}
        <x-card title="Kategori Tersedia">
            <div class="grid grid-cols-2 gap-2">
                @foreach($categories as $cat)
                    <div class="flex items-center gap-2 rounded-lg bg-slate-50 px-2.5 py-2">
                        <span class="h-2 w-2 flex-shrink-0 rounded-full
                            {{ match($cat->color) {
                                'blue'   => 'bg-blue-400',
                                'teal'   => 'bg-teal-400',
                                'purple' => 'bg-purple-400',
                                'indigo' => 'bg-indigo-400',
                                'green'  => 'bg-green-400',
                                'cyan'   => 'bg-cyan-400',
                                'yellow' => 'bg-yellow-400',
                                'red'    => 'bg-red-400',
                                'orange' => 'bg-orange-400',
                                default  => 'bg-slate-400',
                            } }}">
                        </span>
                        <span class="truncate text-xs font-medium text-slate-600">{{ $cat->name }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        {{-- Priority guide --}}
        <x-card title="Panduan Prioritas">
            <div class="space-y-2.5">
                @foreach([
                    ['level' => 'Rendah',  'color' => 'bg-slate-100 text-slate-600',   'desc' => 'Tidak mengganggu kegiatan, bisa ditangani kapan saja.'],
                    ['level' => 'Sedang',  'color' => 'bg-blue-100 text-blue-700',     'desc' => 'Perlu perhatian tetapi tidak mendesak.'],
                    ['level' => 'Tinggi',  'color' => 'bg-orange-100 text-orange-700', 'desc' => 'Mengganggu kegiatan belajar, perlu segera ditangani.'],
                    ['level' => 'Kritis',  'color' => 'bg-red-100 text-red-700',       'desc' => 'Berbahaya atau melumpuhkan kegiatan sekolah.'],
                ] as $p)
                    <div class="flex items-start gap-2.5">
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $p['color'] }} flex-shrink-0">{{ $p['level'] }}</span>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $p['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
function aspirasiform() {
    return {
        title: '{{ old('title', '') }}',
        description: '{{ old('description', '') }}',
        submitting: false,
        submitForm(e) {
            this.submitting = true;
            e.target.submit();
        }
    }
}

function imageUpload() {
    return {
        previews: [],
        files: [],
        isDragging: false,
        handleFiles(fileList) {
            const allowed = Array.from(fileList).slice(0, 5 - this.previews.length);
            allowed.forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },
        handleDrop(e) {
            this.isDragging = false;
            this.handleFiles(e.dataTransfer.files);
        },
        removeImage(index) {
            this.previews.splice(index, 1);
        }
    }
}
</script>
@endpush
