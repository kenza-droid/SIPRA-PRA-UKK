@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan dan statistik aspirasi masuk')

@section('content')
@php
    $attentionPayload = $needAttention->map(function ($c) {
        return [
            'id' => $c->id,
            'number' => $c->complaint_number,
            'title' => $c->title,
            'description' => $c->description,
            'location' => $c->location,
            'category' => $c->category->name,
            'student_name' => $c->user->name,
            'student_class' => $c->user->class,
            'student_avatar' => $c->user->avatar_url,
            'status' => $c->status,
            'status_label' => $c->status_label,
            'priority' => $c->priority,
            'priority_label' => $c->priority_label,
            'progress' => (int) $c->progress_percentage,
            'days_open' => (int) $c->days_open,
            'created_human' => $c->created_at->diffForHumans(),
            'created_date' => $c->created_at->format('d M Y, H:i'),
            'url' => route('admin.complaints.show', $c),
        ];
    })->values();
@endphp

<div x-data="adminDashboard({{ Js::from($attentionPayload) }})"
     x-on:charts-ready.window="chartsReady = true"
     x-on:keydown.escape.window="closePreview()">

    {{-- ── Welcome row ─────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Selamat datang, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-0.5 text-sm text-slate-500">
                {{ now()->translatedFormat('l, d F Y') }} — Pantau semua aspirasi masuk dari sini.
            </p>
        </div>
        <a href="{{ route('admin.complaints.index') }}"
           @click="navigating = 'kelola-all'"
           class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-primary-700 active:scale-[0.98] transition-all duration-150">
            <span x-show="navigating !== 'kelola-all'">@include('layouts.icons', ['icon' => 'inbox-stack', 'class' => 'h-4 w-4'])</span>
            <svg x-show="navigating === 'kelola-all'" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>Kelola Aspirasi</span>
        </a>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────── --}}
    <div class="mb-6 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6">
        <x-stat-card label="Total Aspirasi"    :value="$stats['total']"           icon="inbox-stack"  color="indigo"  />
        <x-stat-card label="Menunggu"          :value="$stats['menunggu']"         icon="clock"        color="amber"   />
        <x-stat-card label="Sedang Diproses"   :value="$stats['diproses']"         icon="cog"          color="blue"    />
        <x-stat-card label="Dalam Perbaikan"   :value="$stats['dalam_perbaikan']"  icon="wrench"       color="orange"  />
        <x-stat-card label="Selesai Bulan Ini" :value="$stats['selesai_bulan']"    icon="check-circle" color="emerald" />
        <x-stat-card label="Total Siswa"       :value="$stats['total_siswa']"      icon="users"        color="purple"  />
    </div>

    {{-- ── Charts Row ──────────────────────────────────────────── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Monthly chart (2/3 width) --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)] lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Aspirasi per Bulan</h3>
                    <p class="text-xs text-slate-400">6 bulan terakhir</p>
                </div>
            </div>
            <div class="relative h-56">
                <div x-show="!chartsReady" class="absolute inset-0 flex items-end gap-3 px-4 pb-6 pt-4">
                    <div class="h-1/2 flex-1 rounded-lg skeleton-shimmer"></div>
                    <div class="h-3/4 flex-1 rounded-lg skeleton-shimmer"></div>
                    <div class="h-2/5 flex-1 rounded-lg skeleton-shimmer"></div>
                    <div class="h-4/5 flex-1 rounded-lg skeleton-shimmer"></div>
                    <div class="h-3/5 flex-1 rounded-lg skeleton-shimmer"></div>
                    <div class="h-full flex-1 rounded-lg skeleton-shimmer"></div>
                </div>
                <canvas id="monthlyChart" class="transition-opacity duration-300" :class="chartsReady ? 'opacity-100' : 'opacity-0'"></canvas>
            </div>
        </div>

        {{-- Status donut --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-slate-800">Distribusi Status</h3>
                <p class="text-xs text-slate-400">Semua waktu</p>
            </div>
            <div class="relative mx-auto h-44 w-44">
                <div x-show="!chartsReady" class="absolute inset-3 rounded-full skeleton-shimmer"></div>
                <canvas id="statusChart" class="transition-opacity duration-300" :class="chartsReady ? 'opacity-100' : 'opacity-0'"></canvas>
            </div>
            <div class="mt-4 space-y-1.5">
                @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
                    @php $count = $statusData[$key] ?? 0; @endphp
                    @if($count > 0)
                        <div class="flex items-center justify-between rounded-lg px-2 py-1 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 flex-shrink-0 rounded-full
                                    {{ match($key) {
                                        'menunggu'        => 'bg-amber-400',
                                        'ditinjau'        => 'bg-sky-400',
                                        'diproses'        => 'bg-indigo-400',
                                        'dalam_perbaikan' => 'bg-orange-400',
                                        'selesai'         => 'bg-emerald-500',
                                        'ditolak'         => 'bg-rose-400',
                                        default           => 'bg-slate-400',
                                    } }}">
                                </span>
                                <span class="text-xs text-slate-600">{{ $cfg['label'] }}</span>
                            </div>
                            <span class="text-xs font-bold text-slate-700 tabular-nums">{{ $count }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Second row: Category + Activity ────────────────────── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Category bar chart --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-slate-800">Aspirasi per Kategori</h3>
                <p class="text-xs text-slate-400">Total per kategori</p>
            </div>
            <div class="relative h-52">
                <div x-show="!chartsReady" class="absolute inset-0 flex flex-col justify-between py-2">
                    <div class="h-5 w-4/5 rounded-md skeleton-shimmer"></div>
                    <div class="h-5 w-3/5 rounded-md skeleton-shimmer"></div>
                    <div class="h-5 w-2/3 rounded-md skeleton-shimmer"></div>
                    <div class="h-5 w-1/2 rounded-md skeleton-shimmer"></div>
                    <div class="h-5 w-2/5 rounded-md skeleton-shimmer"></div>
                </div>
                <canvas id="categoryChart" class="transition-opacity duration-300" :class="chartsReady ? 'opacity-100' : 'opacity-0'"></canvas>
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="rounded-2xl bg-white border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Aktivitas Terbaru</h3>
                    <p class="text-xs text-slate-400">10 perubahan terakhir</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100/80">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50/70 transition-colors duration-150">
                        <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full
                            {{ match($activity->action_color) {
                                'indigo'  => 'bg-indigo-50 text-indigo-600',
                                'blue'    => 'bg-sky-50 text-sky-600',
                                'purple'  => 'bg-purple-50 text-purple-600',
                                'emerald' => 'bg-emerald-50 text-emerald-600',
                                'amber'   => 'bg-amber-50 text-amber-600',
                                default   => 'bg-slate-100 text-slate-500',
                            } }} mt-0.5">
                            @include('layouts.icons', ['icon' => $activity->action_icon, 'class' => 'h-3.5 w-3.5'])
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-slate-700">
                                {{ $activity->action_label }}
                            </p>
                            <p class="truncate text-xs text-slate-400">
                                {{ $activity->complaint->title ?? 'N/A' }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $activity->changedBy->name }} · {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if($activity->new_status)
                            <x-status-badge :status="$activity->new_status" size="xs" />
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-slate-400">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Butuh Perhatian ─────────────────────────────────────── --}}
    <div class="rounded-2xl bg-white border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)] overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Aspirasi yang Membutuhkan Perhatian</h3>
                    <p class="text-xs text-slate-400">Belum ditinjau, prioritas kritis, atau sudah lama</p>
                </div>
            </div>
            <a href="{{ route('admin.complaints.index', ['status' => 'menunggu']) }}"
               class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">Lihat semua →</a>
        </div>

        @if($needAttention->isEmpty())
            <div class="px-5 py-10 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 mb-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-600">Semua aspirasi sudah ditangani</p>
                <p class="mt-1 text-xs text-slate-400">Tidak ada aspirasi yang membutuhkan perhatian segera.</p>
            </div>
        @else
            {{-- Smooth filter tabs --}}
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

            {{-- Loading skeleton while switching tab --}}
            <div x-show="tabLoading" x-cloak class="divide-y divide-slate-100">
                @for($i = 0; $i < 3; $i++)
                    <div class="flex items-center gap-4 px-5 py-4">
                        <div class="h-9 w-9 rounded-full skeleton-shimmer flex-shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 w-52 rounded-md skeleton-shimmer"></div>
                            <div class="h-3 w-40 rounded-md skeleton-shimmer"></div>
                        </div>
                        <div class="h-6 w-24 rounded-full skeleton-shimmer"></div>
                    </div>
                @endfor
            </div>

            {{-- Filtered rows --}}
            <div x-show="!tabLoading"
                 x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="divide-y divide-slate-100">

                <template x-if="filteredAttention.length === 0">
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm font-medium text-slate-600">Tidak ada pengaduan pada kategori ini</p>
                        <p class="mt-1 text-xs text-slate-400">Pilih tab lain atau buka daftar lengkap pengaduan.</p>
                    </div>
                </template>

                <template x-for="c in filteredAttention" :key="c.id">
                    <div class="group flex items-center gap-4 px-5 py-4 hover:bg-slate-50/80 transition-colors duration-150">
                        {{-- Priority indicator --}}
                        <div class="flex-shrink-0 w-1.5 self-stretch rounded-full"
                             :class="priorityBarClass(c.priority)">
                        </div>

                        {{-- Avatar --}}
                        <img :src="c.student_avatar" :alt="c.student_name"
                             class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-100">

                        {{-- Info --}}
                        <button type="button" @click="openPreview(c)" class="min-w-0 flex-1 text-left focus:outline-none">
                            <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-primary-700 transition-colors duration-150"
                               x-text="c.title"></p>
                            <div class="mt-0.5 flex flex-wrap items-center gap-x-2.5 gap-y-0.5 text-xs text-slate-400">
                                <span x-text="c.student_name"></span>
                                <span>·</span>
                                <span x-text="c.category"></span>
                                <span>·</span>
                                <span x-text="c.created_human"></span>
                            </div>
                        </button>

                        {{-- Badges --}}
                        <div class="hidden sm:flex flex-shrink-0 items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1"
                                  :class="priorityBadgeClass(c.priority)"
                                  x-text="c.priority_label"></span>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1"
                                  :class="statusBadgeClass(c.status)">
                                <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(c.status)"></span>
                                <span x-text="c.status_label"></span>
                            </span>
                        </div>

                        {{-- Quick preview + CTA --}}
                        <div class="flex flex-shrink-0 items-center gap-1.5">
                            <button type="button"
                                    @click="openPreview(c)"
                                    title="Pratinjau detail"
                                    aria-label="Pratinjau detail"
                                    class="rounded-lg border border-slate-200/80 bg-white p-2 text-slate-500 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 active:scale-95 transition-all duration-150">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                            <a :href="c.url"
                               title="Kelola aspirasi"
                               aria-label="Kelola aspirasi"
                               class="rounded-lg border border-slate-200/80 bg-white p-2 text-slate-400 hover:border-primary-200 hover:text-primary-600 group-hover:translate-x-0.5 active:scale-95 transition-all duration-150">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>

    {{-- ── Quick Detail Modal (Admin) ──────────────────────────── --}}
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
                <div class="min-w-0 pr-4">
                    <p class="font-mono text-[11px] text-slate-400" x-text="selected?.number"></p>
                    <h3 class="text-base font-semibold text-slate-900 truncate" x-text="selected?.title"></h3>
                </div>
                <button type="button" @click="closePreview()" aria-label="Tutup modal"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Skeleton --}}
            <div x-show="modalLoading" class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full skeleton-shimmer"></div>
                    <div class="space-y-1.5 flex-1">
                        <div class="h-3.5 w-36 rounded-md skeleton-shimmer"></div>
                        <div class="h-3 w-24 rounded-md skeleton-shimmer"></div>
                    </div>
                </div>
                <div class="h-24 w-full rounded-xl skeleton-shimmer"></div>
            </div>

            {{-- Content --}}
            <div x-show="!modalLoading && selected"
                 x-transition:enter="transition ease-out duration-150 motion-reduce:transition-none"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="p-6 space-y-4">

                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3.5 py-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <img :src="selected?.student_avatar" :alt="selected?.student_name"
                             class="h-8 w-8 rounded-full object-cover ring-2 ring-white">
                        <div class="min-w-0">
                            <p class="truncate text-xs font-semibold text-slate-800" x-text="selected?.student_name"></p>
                            <p class="text-[11px] text-slate-400" x-text="selected?.student_class || 'Siswa'"></p>
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400" x-text="selected?.created_human"></span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1"
                          :class="statusBadgeClass(selected?.status)">
                        <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(selected?.status)"></span>
                        <span x-text="selected?.status_label"></span>
                    </span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1"
                          :class="priorityBadgeClass(selected?.priority)"
                          x-text="'Prioritas: ' + (selected?.priority_label || '')"></span>
                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700"
                          x-text="selected?.category"></span>
                </div>

                <div class="rounded-xl border border-slate-200/70 bg-slate-50/70 p-4">
                    <p class="text-xs font-medium text-slate-400 mb-1">Deskripsi Laporan</p>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line max-h-40 overflow-y-auto scrollbar-thin"
                       x-text="selected?.description"></p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-xl bg-slate-50 px-3.5 py-2.5">
                        <p class="text-slate-400">Lokasi</p>
                        <p class="mt-0.5 font-semibold text-slate-700 truncate" x-text="selected?.location"></p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3.5 py-2.5">
                        <p class="text-slate-400">Tanggal Masuk</p>
                        <p class="mt-0.5 font-semibold text-slate-700" x-text="selected?.created_date"></p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 bg-slate-50/60 px-6 py-3.5">
                <button type="button" @click="closePreview()"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-all duration-150">
                    Tutup
                </button>
                <a :href="selected?.url"
                   @click="navigating = 'modal-manage'"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-700 active:scale-[0.98] transition-all duration-150">
                    <svg x-show="navigating === 'modal-manage'" x-cloak class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span>Kelola Pengaduan →</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function adminDashboard(items) {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    return {
        items,
        chartsReady: false,
        activeTab: 'semua',
        tabLoading: false,
        previewOpen: false,
        modalLoading: false,
        selected: null,
        navigating: null,
        tabs: [
            { key: 'semua', label: 'Semua' },
            { key: 'menunggu', label: 'Menunggu' },
            { key: 'kritis', label: 'Prioritas Kritis' },
            { key: 'lama', label: 'Sudah Lama' },
        ],
        get filteredAttention() {
            if (this.activeTab === 'semua') return this.items;
            if (this.activeTab === 'menunggu') return this.items.filter(i => i.status === 'menunggu');
            if (this.activeTab === 'kritis') return this.items.filter(i => i.priority === 'kritis');
            if (this.activeTab === 'lama') return this.items.filter(i => i.days_open >= 7);
            return this.items;
        },
        tabCount(key) {
            if (key === 'semua') return this.items.length;
            if (key === 'menunggu') return this.items.filter(i => i.status === 'menunggu').length;
            if (key === 'kritis') return this.items.filter(i => i.priority === 'kritis').length;
            if (key === 'lama') return this.items.filter(i => i.days_open >= 7).length;
            return 0;
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
        priorityBarClass(priority) {
            return {
                kritis: 'bg-rose-500',
                tinggi: 'bg-orange-400',
                sedang: 'bg-sky-400',
            }[priority] || 'bg-slate-200';
        },
        priorityBadgeClass(priority) {
            return {
                kritis: 'bg-rose-50 text-rose-700 ring-rose-200/70',
                tinggi: 'bg-orange-50 text-orange-700 ring-orange-200/70',
                sedang: 'bg-sky-50 text-sky-700 ring-sky-200/70',
                rendah: 'bg-slate-50 text-slate-600 ring-slate-200/70',
            }[priority] || 'bg-slate-50 text-slate-600 ring-slate-200/70';
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

document.addEventListener('DOMContentLoaded', () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const chartAnimation = prefersReducedMotion ? false : { duration: 450 };

    // ── Monthly Chart ────────────────────────────────────────
    const monthlyData = @json($monthlyData);
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => d.label),
            datasets: [{
                label: 'Aspirasi',
                data: monthlyData.map(d => d.total),
                backgroundColor: 'rgba(99,102,241,0.15)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            animation: chartAnimation,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8' } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 }, beginAtZero: true },
            }
        }
    });

    // ── Status Donut ─────────────────────────────────────────
    const statusData = @json($statusData);
    const statusColors = {
        menunggu: '#fbbf24', ditinjau: '#38bdf8', diproses: '#818cf8',
        dalam_perbaikan: '#fb923c', selesai: '#34d399', ditolak: '#fb7185',
    };
    const statusLabels = {
        menunggu: 'Menunggu', ditinjau: 'Ditinjau', diproses: 'Diproses',
        dalam_perbaikan: 'Dalam Perbaikan', selesai: 'Selesai', ditolak: 'Ditolak',
    };
    const sdKeys = Object.keys(statusData).filter(k => statusData[k] > 0);
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: sdKeys.map(k => statusLabels[k] || k),
            datasets: [{
                data: sdKeys.map(k => statusData[k]),
                backgroundColor: sdKeys.map(k => statusColors[k] || '#cbd5e1'),
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            animation: chartAnimation,
            cutout: '66%',
            plugins: { legend: { display: false } }
        }
    });

    // ── Category Bar ─────────────────────────────────────────
    const categoryData = @json($categoryData);
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: categoryData.map(d => d.category?.name || 'Unknown'),
            datasets: [{
                label: 'Jumlah',
                data: categoryData.map(d => d.total),
                backgroundColor: [
                    '#818cf8','#34d399','#fbbf24','#fb7185','#38bdf8',
                    '#a78bfa','#fb923c','#2dd4bf','#f472b6','#94a3b8',
                ],
                borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, indexAxis: 'y',
            animation: chartAnimation,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 }, beginAtZero: true },
                y: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
            }
        }
    });

    window.dispatchEvent(new CustomEvent('charts-ready'));
});
</script>
@endpush
