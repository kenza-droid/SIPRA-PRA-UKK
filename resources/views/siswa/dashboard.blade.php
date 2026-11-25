@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan pengaduan dan progres perbaikan fasilitas sekolah')

@section('content')
@php
    $user = auth()->user();
    $recentPayload = $recentComplaints->map(function ($c) {
        return [
            'id' => $c->id,
            'number' => $c->complaint_number,
            'title' => $c->title,
            'description' => $c->description,
            'location' => $c->location,
            'category' => $c->category->name,
            'status' => $c->status,
            'status_label' => $c->status_label,
            'priority' => $c->priority,
            'priority_label' => $c->priority_label,
            'progress' => (int) $c->progress_percentage,
            'created_human' => $c->created_at->diffForHumans(),
            'created_date' => $c->created_at->format('d M Y, H:i'),
            'url' => route('siswa.complaints.show', $c),
        ];
    })->values();
@endphp

<div x-data="siswaDashboard({{ Js::from($recentPayload) }})" x-on:keydown.escape.window="closePreview()">

    {{-- ── Welcome Banner ─────────────────────────────────────────── --}}
    <div class="relative mb-6 overflow-hidden rounded-2xl border border-indigo-100/90 bg-gradient-to-br from-[#eef3ff] via-[#f7f9ff] to-white p-6 sm:p-7">
        <div class="pointer-events-none absolute -right-12 -top-12 h-52 w-52 rounded-full bg-indigo-200/25 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-14 left-1/3 h-44 w-44 rounded-full bg-sky-200/25 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="max-w-xl">
                <div class="flex flex-wrap items-center gap-2 mb-2.5">
                    <span class="text-xs font-medium text-indigo-600/90">Selamat datang kembali</span>
                    @if($user->class)
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/90 border border-indigo-100 px-2.5 py-0.5 text-[11px] font-semibold text-indigo-700">
                            <svg class="h-3 w-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                            </svg>
                            Kelas {{ $user->class }}
                        </span>
                    @endif
                </div>

                <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                    {{ $user->name }}
                </h1>
                <p class="mt-1.5 text-sm text-slate-600 leading-relaxed max-w-lg">
                    Laporkan kerusakan fasilitas sekolah dan pantau progres perbaikannya dalam satu tempat.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('siswa.complaints.create') }}"
                       @click="navigating = 'create'"
                       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-xs hover:bg-primary-700 active:scale-[0.98] transition-all duration-150">
                        <svg x-show="navigating !== 'create'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <svg x-show="navigating === 'create'" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Buat Pengaduan</span>
                    </a>
                    <a href="{{ route('siswa.complaints.index') }}"
                       @click="navigating = 'history'"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white/90 px-4 py-2 text-xs sm:text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 active:scale-[0.98] transition-all duration-150">
                        <svg x-show="navigating === 'history'" x-cloak class="h-3.5 w-3.5 animate-spin text-slate-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Lihat Riwayat</span>
                    </a>
                </div>
            </div>

            <div class="hidden md:flex flex-shrink-0 items-end justify-end pr-2">
                <img src="{{ asset('asset/scool.png') }}"
                     alt="Ilustrasi Sekolah"
                     class="h-32 lg:h-36 w-auto object-contain opacity-95 select-none pointer-events-none">
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────────── --}}
    <div class="mb-6 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-5">
        <x-stat-card label="Total Pengaduan"   :value="$stats['total']"           icon="inbox-stack"  color="indigo" />
        <x-stat-card label="Menunggu"          :value="$stats['menunggu']"         icon="clock"        color="amber"  />
        <x-stat-card label="Sedang Diproses"   :value="$stats['diproses']"         icon="cog"          color="blue"   />
        <x-stat-card label="Dalam Perbaikan"   :value="$stats['dalam_perbaikan']"  icon="wrench"       color="orange" />
        <x-stat-card label="Selesai"           :value="$stats['selesai']"          icon="check-circle" color="emerald"/>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Pengaduan Terbaru ────────────────────────────────────── --}}
        <div class="lg:col-span-2">
            <x-card title="Pengaduan Terbaru" subtitle="5 laporan terakhir yang kamu kirim" :padding="false">
                <x-slot:action>
                    <a href="{{ route('siswa.complaints.index') }}"
                       class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                        Lihat semua →
                    </a>
                </x-slot:action>

                @if($recentComplaints->isEmpty())
                    <div class="p-5">
                        <x-empty-state
                            title="Belum ada pengaduan"
                            description="Kalau ada fasilitas sekolah yang rusak atau bermasalah, kamu bisa langsung buat laporan di sini."
                            action-route="siswa.complaints.create"
                            action-label="Buat Pengaduan Pertama"
                            icon="inbox-stack" />
                    </div>
                @else
                    {{-- Status filter tabs --}}
                    <div class="flex items-center gap-1.5 overflow-x-auto border-b border-slate-100 bg-slate-50/50 px-5 py-2.5 scrollbar-thin">
                        <template x-for="tab in tabs" :key="tab.key">
                            <button type="button"
                                    @click="switchTab(tab.key)"
                                    class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-150 active:scale-[0.97]"
                                    :class="activeTab === tab.key
                                        ? 'bg-white text-indigo-700 font-semibold shadow-xs ring-1 ring-indigo-200/80'
                                        : 'text-slate-500 hover:bg-white/80 hover:text-slate-800'">
                                <span x-text="tab.label"></span>
                                <span class="rounded-full px-1.5 py-0.5 text-[10px] tabular-nums transition-colors"
                                      :class="activeTab === tab.key ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-200/70 text-slate-600'"
                                      x-text="tabCount(tab.key)"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Skeleton state while switching tab --}}
                    <div x-show="tabLoading" x-cloak class="divide-y divide-slate-100">
                        @for($i = 0; $i < 3; $i++)
                            <div class="flex items-center gap-3.5 px-5 py-4">
                                <div class="h-10 w-10 rounded-xl skeleton-shimmer flex-shrink-0"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 w-48 rounded-md skeleton-shimmer"></div>
                                    <div class="h-3 w-36 rounded-md skeleton-shimmer"></div>
                                </div>
                                <div class="h-6 w-20 rounded-full skeleton-shimmer"></div>
                            </div>
                        @endfor
                    </div>

                    {{-- Filtered list --}}
                    <div x-show="!tabLoading"
                         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="divide-y divide-slate-100">

                        <template x-if="filteredComplaints.length === 0">
                            <div class="px-5 py-10 text-center">
                                <p class="text-sm font-medium text-slate-600">Tidak ada laporan di status ini</p>
                                <p class="mt-1 text-xs text-slate-400">Cek kategori lain atau lihat seluruh laporanmu di menu Riwayat.</p>
                                <a href="{{ route('siswa.complaints.index') }}"
                                   class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                    Buka Riwayat →
                                </a>
                            </div>
                        </template>

                        <template x-for="item in filteredComplaints" :key="item.id">
                            <div class="group flex items-center gap-3.5 px-5 py-4 hover:bg-slate-50/90 transition-colors duration-150">
                                {{-- Icon --}}
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100/80 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors duration-150">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                </div>

                                {{-- Main clickable area (opens smooth quick-detail modal) --}}
                                <button type="button"
                                        @click="openPreview(item)"
                                        class="min-w-0 flex-1 text-left focus:outline-none">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-primary-700 transition-colors duration-150"
                                           x-text="item.title"></p>
                                        <span class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1"
                                              :class="statusBadgeClass(item.status)">
                                            <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(item.status)"></span>
                                            <span x-text="item.status_label"></span>
                                        </span>
                                    </div>

                                    <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-400">
                                        <span class="font-mono text-[11px] text-slate-500" x-text="item.number"></span>
                                        <span>·</span>
                                        <span x-text="item.category"></span>
                                        <span>·</span>
                                        <span x-text="item.created_human"></span>
                                    </div>

                                    <template x-if="item.progress > 0 && item.progress < 100">
                                        <div class="mt-2.5 flex items-center gap-2.5">
                                            <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                                                <div class="h-full rounded-full bg-indigo-500/90 transition-all duration-500"
                                                     :style="'width: ' + item.progress + '%'"></div>
                                            </div>
                                            <span class="text-[11px] font-semibold text-slate-500 tabular-nums" x-text="item.progress + '%'"></span>
                                        </div>
                                    </template>
                                </button>

                                {{-- Actions --}}
                                <div class="flex flex-shrink-0 items-center gap-1">
                                    <button type="button"
                                            @click="openPreview(item)"
                                            title="Pratinjau cepat"
                                            aria-label="Pratinjau cepat"
                                            class="rounded-lg p-2 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 active:scale-95 transition-all duration-150">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>
                                    <a :href="item.url"
                                       title="Buka halaman detail"
                                       aria-label="Buka halaman detail"
                                       class="rounded-lg p-2 text-slate-300 hover:bg-slate-100 hover:text-primary-600 group-hover:translate-x-0.5 transition-all duration-150">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- ── Timeline Progres Aktif ──────────────────────────────── --}}
        <div class="space-y-6">
            <x-card title="Progres Perbaikan" subtitle="Laporan aktif yang sedang ditangani">
                @if($activeComplaint)
                    <div class="mb-5 rounded-xl border border-slate-200/70 bg-slate-50/80 p-3.5">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-[11px] text-slate-400">{{ $activeComplaint->complaint_number }}</span>
                            <x-status-badge :status="$activeComplaint->status" size="xs" />
                        </div>
                        <p class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $activeComplaint->title }}</p>
                        <div class="mt-2.5">
                            <x-progress-bar :percentage="$activeComplaint->progress_percentage" size="md" />
                        </div>
                    </div>

                    <x-timeline :steps="$activeComplaint->progress_steps" />

                    <a href="{{ route('siswa.complaints.show', $activeComplaint) }}"
                       @click="navigating = 'active-detail'"
                       class="mt-5 flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 active:scale-[0.98] transition-all duration-150">
                        <svg x-show="navigating === 'active-detail'" x-cloak class="h-3.5 w-3.5 animate-spin text-slate-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Lihat Detail Lengkap</span>
                        <svg x-show="navigating !== 'active-detail'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @else
                    <x-empty-state
                        title="Tidak ada laporan aktif"
                        description="Semua laporanmu sudah selesai ditangani atau belum ada laporan baru."
                        icon="check-circle" />
                @endif
            </x-card>

            {{-- Quick info --}}
            <x-card title="Tips Pengaduan">
                <ul class="space-y-3">
                    @foreach([
                        ['icon' => 'plus-circle', 'text' => 'Tulis lokasi ruang, gedung, dan lantainya dengan jelas'],
                        ['icon' => 'eye',         'text' => 'Ceritakan kondisi kerusakannya supaya mudah dicek'],
                        ['icon' => 'chart-bar',   'text' => 'Lampirkan foto kondisi di lapangan jika ada'],
                        ['icon' => 'clock',       'text' => 'Pantau status perbaikan lewat menu Riwayat'],
                    ] as $tip)
                        <li class="flex items-center gap-3">
                            <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-50/80 text-indigo-600">
                                @include('layouts.icons', ['icon' => $tip['icon'], 'class' => 'h-3.5 w-3.5'])
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $tip['text'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        </div>
    </div>

    {{-- ── Quick Detail Modal ───────────────────────────────────── --}}
    <div x-show="previewOpen"
         x-cloak
         role="dialog"
         aria-modal="true"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div x-show="previewOpen"
             x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closePreview()"
             class="absolute inset-0 bg-slate-900/40 backdrop-blur-xs">
        </div>

        {{-- Panel --}}
        <div x-show="previewOpen"
             x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none motion-reduce:transform-none"
             x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none motion-reduce:transform-none"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
             class="relative w-full max-w-lg rounded-2xl border border-slate-200/80 bg-white shadow-xl overflow-hidden">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <p class="font-mono text-[11px] text-slate-400" x-text="selected?.number"></p>
                    <h3 class="text-base font-semibold text-slate-900 line-clamp-1" x-text="selected?.title"></h3>
                </div>
                <button type="button" @click="closePreview()" aria-label="Tutup modal"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Loading skeleton inside modal --}}
            <div x-show="modalLoading" class="p-6 space-y-4">
                <div class="flex gap-2">
                    <div class="h-6 w-24 rounded-full skeleton-shimmer"></div>
                    <div class="h-6 w-20 rounded-full skeleton-shimmer"></div>
                </div>
                <div class="h-20 w-full rounded-xl skeleton-shimmer"></div>
                <div class="h-4 w-2/3 rounded-md skeleton-shimmer"></div>
            </div>

            {{-- Loaded content --}}
            <div x-show="!modalLoading && selected"
                 x-transition:enter="transition ease-out duration-150 motion-reduce:transition-none"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="p-6 space-y-4">

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1"
                          :class="statusBadgeClass(selected?.status)">
                        <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(selected?.status)"></span>
                        <span x-text="selected?.status_label"></span>
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600"
                          x-text="'Prioritas: ' + (selected?.priority_label || '')"></span>
                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700"
                          x-text="selected?.category"></span>
                </div>

                <div class="rounded-xl border border-slate-200/70 bg-slate-50/70 p-4">
                    <p class="text-xs font-medium text-slate-400 mb-1">Deskripsi Masalah</p>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line max-h-40 overflow-y-auto scrollbar-thin"
                       x-text="selected?.description"></p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-xl bg-slate-50 px-3.5 py-2.5">
                        <p class="text-slate-400">Lokasi</p>
                        <p class="mt-0.5 font-semibold text-slate-700 truncate" x-text="selected?.location"></p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3.5 py-2.5">
                        <p class="text-slate-400">Tanggal Kirim</p>
                        <p class="mt-0.5 font-semibold text-slate-700" x-text="selected?.created_date"></p>
                    </div>
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Progres Perbaikan</span>
                        <span class="font-semibold text-slate-700 tabular-nums" x-text="(selected?.progress ?? 0) + '%'"></span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-indigo-500 transition-all duration-500"
                             :style="'width: ' + (selected?.progress ?? 0) + '%'"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 bg-slate-50/60 px-6 py-3.5">
                <button type="button" @click="closePreview()"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-all duration-150">
                    Tutup
                </button>
                <a :href="selected?.url"
                   @click="navigating = 'modal-detail'"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-700 active:scale-[0.98] transition-all duration-150">
                    <svg x-show="navigating === 'modal-detail'" x-cloak class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span>Buka Halaman Detail →</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function siswaDashboard(items) {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    return {
        items,
        activeTab: 'semua',
        tabLoading: false,
        previewOpen: false,
        modalLoading: false,
        selected: null,
        navigating: null,
        tabs: [
            { key: 'semua', label: 'Semua' },
            { key: 'menunggu', label: 'Menunggu' },
            { key: 'diproses', label: 'Diproses' },
            { key: 'dalam_perbaikan', label: 'Perbaikan' },
            { key: 'selesai', label: 'Selesai' },
        ],
        get filteredComplaints() {
            if (this.activeTab === 'semua') return this.items;
            if (this.activeTab === 'diproses') {
                return this.items.filter(i => i.status === 'diproses' || i.status === 'ditinjau');
            }
            return this.items.filter(i => i.status === this.activeTab);
        },
        tabCount(key) {
            if (key === 'semua') return this.items.length;
            if (key === 'diproses') {
                return this.items.filter(i => i.status === 'diproses' || i.status === 'ditinjau').length;
            }
            return this.items.filter(i => i.status === key).length;
        },
        switchTab(key) {
            if (this.activeTab === key) return;
            this.activeTab = key;
            if (prefersReducedMotion) return;
            this.tabLoading = true;
            setTimeout(() => { this.tabLoading = false; }, 120);
        },
        openPreview(item) {
            this.selected = item;
            this.previewOpen = true;
            if (prefersReducedMotion) {
                this.modalLoading = false;
                return;
            }
            this.modalLoading = true;
            setTimeout(() => { this.modalLoading = false; }, 110);
        },
        closePreview() {
            this.previewOpen = false;
        },
        statusBadgeClass(status) {
            return {
                menunggu: 'bg-amber-50 text-amber-700 ring-amber-200/70',
                ditinjau: 'bg-sky-50 text-sky-700 ring-sky-200/70',
                diproses: 'bg-indigo-50 text-indigo-700 ring-indigo-200/70',
                dalam_perbaikan: 'bg-orange-50 text-orange-700 ring-orange-200/70',
                selesai: 'bg-emerald-50 text-emerald-700 ring-emerald-200/70',
                ditolak: 'bg-rose-50 text-rose-700 ring-rose-200/70',
            }[status] || 'bg-slate-50 text-slate-700 ring-slate-200/70';
        },
        statusDotClass(status) {
            return {
                menunggu: 'bg-amber-400',
                ditinjau: 'bg-sky-400',
                diproses: 'bg-indigo-400',
                dalam_perbaikan: 'bg-orange-400',
                selesai: 'bg-emerald-500',
                ditolak: 'bg-rose-400',
            }[status] || 'bg-slate-400';
        },
    };
}
</script>
@endpush
