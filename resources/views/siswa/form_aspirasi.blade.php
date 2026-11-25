@extends('layouts.app')

@section('title', 'Buat Pengaduan Baru')
@section('page-title', 'Buat Pengaduan Baru')
@section('page-subtitle', 'Laporkan fasilitas sekolah yang rusak atau bermasalah biar bisa segera diperbaiki.')

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

            @if ($errors->any())
                <div role="alert" class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5 text-red-800 shadow-sm">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <div class="text-sm">
                        <p class="font-semibold">Pengaduan belum bisa dikirim</p>
                        <p class="mt-0.5 text-xs text-red-700">Masih ada bagian yang perlu dilengkapi atau diperbaiki. Cek kolom yang ditandai merah di bawah, ya.</p>
                    </div>
                </div>
            @endif

            <x-card>
                <x-slot:title>Isi Detail Pengaduan</x-slot:title>

                <div class="space-y-5">

                    {{-- Identitas & No. Telepon Pelapor --}}
                    <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                                Informasi Pelapor
                            </p>
                            <span class="text-[11px] text-slate-400">Otomatis terhubung dengan akun Anda</span>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 mb-3">
                            <div class="rounded-lg bg-white px-3 py-2 border border-slate-200/60">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Nama Siswa</p>
                                <p class="text-xs font-semibold text-slate-700 truncate">{{ auth()->user()->name }}</p>
                            </div>
                            <div class="rounded-lg bg-white px-3 py-2 border border-slate-200/60">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">NISN</p>
                                <p class="text-xs font-semibold text-slate-700 font-mono truncate">{{ auth()->user()->nisn ?? '—' }}</p>
                            </div>
                            <div class="rounded-lg bg-white px-3 py-2 border border-slate-200/60">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Kelas</p>
                                <p class="text-xs font-semibold text-slate-700 truncate">{{ auth()->user()->class ?? '—' }}</p>
                            </div>
                        </div>
                        <x-form-input name="phone" label="No. Telepon / WhatsApp" hint="Ditampilkan ke petugas agar mudah dihubungi jika perlu konfirmasi tambahan.">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                    </svg>
                                </div>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       maxlength="20"
                                       placeholder="cth: 081234567890"
                                       class="w-full rounded-xl border-[1.5px] border-slate-200 bg-white pl-10 pr-4 py-2 text-sm font-mono text-slate-800 transition focus:border-primary-500 focus:outline-none focus:ring-3 focus:ring-primary-100 @error('phone') border-red-400 bg-red-50 @enderror">
                            </div>
                        </x-form-input>
                    </div>

                    {{-- Judul --}}
                    <x-form-input name="title" label="Judul Pengaduan" :required="true" hint="Tulis singkat dan jelas, maksimal 200 karakter.">
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title') }}"
                               maxlength="200"
                               placeholder="Contoh: Proyektor di ruang XII RPL 1 mati total"
                               x-model="title"
                               class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('title') border-red-400 bg-red-50 @enderror"
                               required>
                        <div class="mt-1 flex justify-end">
                            <span class="text-xs text-slate-400" x-text="title.length + '/200'"></span>
                        </div>
                    </x-form-input>

                    {{-- Kategori & Prioritas --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-form-input name="category_id" label="Kategori Fasilitas" :required="true">
                            <select name="category_id" id="category_id"
                                    class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('category_id') border-red-400 bg-red-50 @enderror"
                                    required>
                                <option value="">Pilih kategori fasilitas</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </x-form-input>

                        <x-form-input name="priority" label="Seberapa Mendesak?" :required="true">
                            <select name="priority" id="priority"
                                    class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('priority') border-red-400 bg-red-50 @enderror"
                                    required>
                                <option value="">Pilih tingkat prioritas</option>
                                <option value="rendah"  {{ old('priority') === 'rendah'  ? 'selected' : '' }}>Rendah — Tidak mendesak</option>
                                <option value="sedang"  {{ old('priority') === 'sedang'  ? 'selected' : '' }}>Sedang — Perlu perhatian</option>
                                <option value="tinggi"  {{ old('priority') === 'tinggi'  ? 'selected' : '' }}>Tinggi — Mengganggu belajar</option>
                                <option value="kritis"  {{ old('priority') === 'kritis'  ? 'selected' : '' }}>Kritis — Darurat / bahaya</option>
                            </select>
                        </x-form-input>
                    </div>

                    {{-- Lokasi --}}
                    <x-form-input name="location" label="Lokasi" :required="true" hint="Contoh: Ruang XII RPL 1, Gedung B Lantai 2">
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
                                   placeholder="Tulis nama ruangan, gedung, dan lantainya"
                                   class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm text-slate-800 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('location') border-red-400 bg-red-50 @enderror"
                                   required>
                        </div>
                    </x-form-input>

                    {{-- Deskripsi --}}
                    <x-form-input name="description" label="Ceritakan Masalahnya" :required="true" hint="Minimal 20 karakter. Ceritakan kondisinya supaya petugas tahu apa yang harus diperbaiki.">
                        <textarea name="description"
                                  id="description"
                                  rows="5"
                                  maxlength="2000"
                                  placeholder="Contoh: AC di ruang XII RPL 1 sudah tidak dingin sejak 3 hari lalu dan airnya menetes ke meja siswa..."
                                  x-model="description"
                                  class="w-full resize-none rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 leading-relaxed transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('description') border-red-400 bg-red-50 @enderror"
                                  required>{{ old('description') }}</textarea>
                        <div class="mt-1 flex items-center justify-between">
                            <span class="text-xs"
                                  :class="description.length === 0 ? 'text-slate-400' : (description.length < 20 ? 'text-amber-600 font-medium' : 'text-emerald-600')">
                                <span x-show="description.length === 0">Minimal 20 karakter</span>
                                <span x-show="description.length > 0 && description.length < 20" x-text="'Kurang ' + (20 - description.length) + ' karakter lagi (minimal 20)'"></span>
                                <span x-show="description.length >= 20">Penjelasan sudah cukup jelas</span>
                            </span>
                            <span class="text-xs text-slate-400" x-text="description.length + '/2000'"></span>
                        </div>
                    </x-form-input>

                    {{-- Upload Foto --}}
                    <div x-data="imageUpload()">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Foto Kondisi
                            <span class="ml-1 text-xs font-normal text-slate-400">(Opsional, maks. 5 foto, tiap foto maks. 3 MB)</span>
                        </label>

                        {{-- Client-side upload error alert --}}
                        <div x-show="uploadError" x-cloak role="alert"
                             x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="mb-3 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-xs text-amber-800">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <p class="flex-1 leading-relaxed" x-text="uploadError"></p>
                            <button type="button" @click="uploadError = ''" aria-label="Tutup peringatan"
                                    class="text-amber-600 hover:text-amber-800 font-semibold">
                                Tutup
                            </button>
                        </div>

                        <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 transition-all"
                             :class="isDragging ? 'border-primary-400 bg-primary-50' : 'hover:border-slate-300'"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleDrop($event)">

                            {{-- Drop zone --}}
                            <div x-show="previews.length === 0" class="px-6 py-9 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-200/80 text-slate-500 mb-3">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                    </svg>
                                </div>
                                <label for="images"
                                       class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-primary-600 shadow-sm ring-1 ring-slate-200 hover:bg-primary-50 transition-colors">
                                    Pilih Foto dari Perangkat
                                </label>
                                <p class="text-xs text-slate-400 mt-2">atau seret foto langsung ke kotak ini</p>
                                <p class="text-[11px] text-slate-400 mt-1">Format JPG, JPEG, PNG, atau WebP</p>
                            </div>

                            {{-- Preview grid --}}
                            <div x-show="previews.length > 0" class="p-4">
                                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                                    <template x-for="(preview, index) in previews" :key="index">
                                        <div class="group relative aspect-square overflow-hidden rounded-xl bg-slate-200">
                                            <img :src="preview" :alt="'Foto kondisi ' + (index + 1)" class="h-full w-full object-cover">
                                            <button type="button"
                                                    @click="removeImage(index)"
                                                    aria-label="Hapus foto"
                                                    title="Hapus foto"
                                                    class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-white opacity-90 shadow-md transition-opacity sm:opacity-0 sm:group-hover:opacity-100 focus:opacity-100">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    {{-- Add more --}}
                                    <template x-if="previews.length < 5">
                                        <label for="images"
                                               aria-label="Tambah foto lagi"
                                               title="Tambah foto lagi"
                                               class="flex aspect-square cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-slate-300 bg-white text-slate-400 hover:border-primary-300 hover:text-primary-500 transition-colors">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            <span class="text-[11px] font-medium">Tambah</span>
                                        </label>
                                    </template>
                                </div>
                                <p class="mt-2 text-xs text-slate-500" x-text="previews.length + ' dari 5 foto dipilih'"></p>
                            </div>

                            <input type="file" name="images[]" id="images" x-ref="imageInput"
                                   multiple accept="image/jpeg,image/png,image/webp" class="sr-only"
                                   @change="handleFiles($event.target.files)">
                        </div>
                        @php
                            $imageErrors = collect($errors->get('images'))
                                ->merge(collect($errors->get('images.*'))->flatten())
                                ->unique();
                        @endphp
                        @foreach($imageErrors as $imageError)
                            <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $imageError }}
                            </p>
                        @endforeach
                    </div>
                </div>
            </x-card>

            {{-- Action buttons --}}
            <div class="mt-4 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                <a href="{{ route('siswa.dashboard') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-xs hover:bg-slate-50 active:scale-[0.98] transition-all duration-150">
                    Batal
                </a>
                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-primary-700 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed transition-all duration-150">
                    <template x-if="submitting">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </template>
                    <template x-if="!submitting">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                    </template>
                    <span x-text="submitting ? 'Mengirim pengaduan...' : 'Kirim Pengaduan'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- ── Info Panel ───────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Tips --}}
        <x-card title="Tips Biar Laporan Cepat Ditangani">
            <ul class="space-y-4">
                @foreach([
                    ['bg' => 'bg-blue-50 text-blue-600',   'icon' => 'eye',          'head' => 'Tulis Lokasi yang Jelas', 'body' => 'Sebutkan ruangan, gedung, dan lantainya supaya petugas tidak salah tempat.'],
                    ['bg' => 'bg-purple-50 text-purple-600','icon' => 'chart-bar',    'head' => 'Jelaskan Masalahnya',     'body' => 'Tulis kerusakannya seperti apa, sejak kapan terjadi, dan dampaknya buat kegiatan belajar.'],
                    ['bg' => 'bg-emerald-50 text-emerald-600','icon' => 'chart-bar',  'head' => 'Lampirkan Foto',          'body' => 'Foto kondisi di lapangan bikin petugas lebih cepat paham kerusakannya.'],
                    ['bg' => 'bg-amber-50 text-amber-600',  'icon' => 'arrow-path',   'head' => 'Cek Perkembangannya',     'body' => 'Kamu bisa pantau progres perbaikan langsung lewat menu Riwayat.'],
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
                    ['level' => 'Rendah',  'color' => 'bg-slate-100 text-slate-600',   'desc' => 'Belum mengganggu belajar, perbaikannya bisa menyusul.'],
                    ['level' => 'Sedang',  'color' => 'bg-blue-100 text-blue-700',     'desc' => 'Perlu diperbaiki, tapi kegiatan belajar masih bisa jalan.'],
                    ['level' => 'Tinggi',  'color' => 'bg-orange-100 text-orange-700', 'desc' => 'Mengganggu kegiatan belajar, sebaiknya segera ditangani.'],
                    ['level' => 'Kritis',  'color' => 'bg-red-100 text-red-700',       'desc' => 'Membahayakan keselamatan atau bikin kegiatan kelas terhenti.'],
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
        title: @js(old('title', '')),
        description: @js(old('description', '')),
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
        uploadError: '',
        handleFiles(fileList) {
            this.uploadError = '';
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const maxSize = 3 * 1024 * 1024;
            const errors = [];

            Array.from(fileList).forEach(file => {
                if (this.files.length >= 5) {
                    errors.push('Maksimal 5 foto yang bisa dilampirkan. Foto selebihnya tidak ditambahkan.');
                    return;
                }
                const extOk = /\.(jpe?g|png|webp)$/i.test(file.name);
                if (!allowedTypes.includes(file.type) && !extOk) {
                    errors.push(`File "${file.name}" belum didukung. Gunakan foto berformat JPG, PNG, atau WebP.`);
                    return;
                }
                if (file.size > maxSize) {
                    errors.push(`Ukuran foto "${file.name}" lebih dari 3 MB. Pilih foto yang lebih kecil, ya.`);
                    return;
                }

                this.files.push(file);
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });

            if (errors.length > 0) {
                this.uploadError = [...new Set(errors)].join(' ');
            }
            this.syncInputFiles();
        },
        handleDrop(e) {
            this.isDragging = false;
            this.handleFiles(e.dataTransfer.files);
        },
        removeImage(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            this.uploadError = '';
            this.syncInputFiles();
        },
        syncInputFiles() {
            const dt = new DataTransfer();
            this.files.forEach(file => dt.items.add(file));
            if (this.$refs.imageInput) {
                this.$refs.imageInput.files = dt.files;
            }
        }
    }
}
</script>
@endpush
