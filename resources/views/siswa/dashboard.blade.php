@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aspirasi dan progres perbaikan Anda')

@section('content')
@php $user = auth()->user(); @endphp

{{-- ── Welcome Banner ─────────────────────────────────────────── --}}
<div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-r from-[#3151eb] via-[#4364f7] to-[#6fb1fc] shadow-xl shadow-indigo-900/10 min-h-[175px] sm:min-h-[195px] flex items-center">

    {{-- Subtle decorative glow / backdrop --}}
    <div class="absolute -top-24 -left-24 h-64 w-64 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full flex flex-col justify-between p-6 sm:p-7 sm:py-8 lg:flex-row lg:items-center">
        
        {{-- Left: Text & Class Info --}}
        <div class="max-w-md lg:max-w-lg z-10">
            <p class="text-sm font-medium text-white/90">
                Selamat datang,
            </p>
            <h1 class="mt-0.5 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                {{ $user->name }}!
            </h1>
            <p class="mt-1.5 text-xs text-indigo-100 sm:text-sm leading-relaxed max-w-sm sm:max-w-md">
                Pantau aspirasi dan progres perbaikan sarana sekolah dalam satu tempat.
            </p>
            @if($user->class)
                <div class="mt-3.5 inline-flex items-center gap-1.5 rounded-full bg-white/20 backdrop-blur-md px-3.5 py-1 text-xs font-semibold text-white ring-1 ring-white/30 shadow-xs">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                    Kelas {{ $user->class }}
                </div>
            @endif
        </div>

        {{-- Right: Handwritten Motto & Ray Details --}}
        <div class="hidden lg:flex relative z-10 flex-col items-center justify-center text-center pr-2 xl:pr-6 select-none shrink-0">
            <div class="relative font-handwriting text-2xl xl:text-3xl leading-snug font-bold text-white/95 drop-shadow-sm">
                {{-- Left ray lines --}}
                <svg class="absolute -left-6 top-1/2 -translate-y-1/2 h-10 w-5 text-white/80" viewBox="0 0 20 40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="16" y1="6" x2="4" y2="10" />
                    <line x1="18" y1="20" x2="2" y2="20" />
                    <line x1="16" y1="34" x2="4" y2="30" />
                </svg>

                <p class="italic tracking-wide">Suara kamu</p>
                <p class="italic tracking-wide">untuk sekolah</p>
                <p class="italic tracking-wide">yang lebih baik</p>

                {{-- Right ray lines --}}
                <svg class="absolute -right-6 top-1/2 -translate-y-1/2 h-10 w-5 text-white/80" viewBox="0 0 20 40" fill="none" stroke-width="2.5" stroke-currentColor" stroke-linecap="round">
                    <line x1="4" y1="6" x2="16" y2="10" />
                    <line x1="2" y1="20" x2="18" y2="20" />
                    <line x1="4" y1="34" x2="16" y2="30" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Center / Center-Right: School Building Image --}}
    <div class="pointer-events-none select-none absolute bottom-0 right-4 sm:right-16 md:right-36 lg:right-56 xl:right-64 h-[155px] sm:h-[180px] md:h-[200px] z-0 opacity-55">
        <img src="{{ asset('asset/scool.png') }}"
             alt="Ilustrasi Sekolah"
             class="h-full w-auto object-contain object-bottom drop-shadow-sm">
    </div>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────────── --}}
<div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
    <x-stat-card label="Total Aspirasi"    :value="$stats['total']"           icon="inbox-stack"  color="indigo" />
    <x-stat-card label="Menunggu"          :value="$stats['menunggu']"         icon="clock"        color="amber"  />
    <x-stat-card label="Sedang Diproses"   :value="$stats['diproses']"         icon="cog"          color="blue"   />
    <x-stat-card label="Dalam Perbaikan"   :value="$stats['dalam_perbaikan']"  icon="wrench"       color="orange" />
    <x-stat-card label="Selesai"           :value="$stats['selesai']"          icon="check-circle" color="emerald"/>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- ── Aspirasi Terbaru ────────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <x-card title="Aspirasi Terbaru" subtitle="5 aspirasi terakhir yang Anda kirim">
            <x-slot:action>
                <a href="{{ route('siswa.complaints.index') }}"
                   class="text-xs font-semibold text-primary-600 hover:text-primary-700">
                    Lihat semua →
                </a>
            </x-slot:action>

            @if($recentComplaints->isEmpty())
                <x-empty-state
                    title="Belum ada aspirasi"
                    description="Mulai sampaikan keluhan atau aspirasi Anda terkait sarana sekolah."
                    action-route="siswa.complaints.create"
                    action-label="Buat Aspirasi Pertama"
                    icon="inbox-stack" />
            @else
                <div class="space-y-3">
                    @foreach($recentComplaints as $complaint)
                        <div class="group flex items-start gap-4 rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:border-primary-100 hover:bg-primary-50/30 hover:shadow-sm">
                            {{-- Category icon --}}
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-primary-700">
                                            {{ $complaint->title }}
                                        </p>
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <span class="text-xs text-slate-400">{{ $complaint->complaint_number }}</span>
                                            <span class="text-slate-200">•</span>
                                            <span class="text-xs text-slate-400">{{ $complaint->category->name }}</span>
                                            <span class="text-slate-200">•</span>
                                            <span class="text-xs text-slate-400">{{ $complaint->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$complaint->status" />
                                </div>
                                {{-- Progress bar --}}
                                @if($complaint->progress_percentage > 0)
                                    <div class="mt-2">
                                        <x-progress-bar :percentage="$complaint->progress_percentage" :show-label="false" size="sm" />
                                        <p class="mt-1 text-right text-[10px] font-semibold text-slate-500">{{ $complaint->progress_percentage }}%</p>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('siswa.complaints.show', $complaint) }}"
                               class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 shadow-sm hover:border-primary-200 hover:text-primary-600 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    {{-- ── Timeline Progres Aktif ──────────────────────────────── --}}
    <div class="space-y-6">
        <x-card title="Progres Perbaikan" subtitle="Aspirasi aktif yang sedang ditangani">
            @if($activeComplaint)
                <div class="mb-4 rounded-xl bg-primary-50 p-3">
                    <p class="text-xs font-semibold text-primary-700 line-clamp-1">{{ $activeComplaint->title }}</p>
                    <div class="mt-1.5">
                        <x-progress-bar :percentage="$activeComplaint->progress_percentage" size="md" />
                    </div>
                </div>

                {{-- Timeline steps --}}
                <x-timeline :steps="$activeComplaint->progress_steps" />

                <a href="{{ route('siswa.complaints.show', $activeComplaint) }}"
                   class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border border-primary-200 bg-primary-50 py-2.5 text-sm font-semibold text-primary-700 hover:bg-primary-100 transition-colors">
                    Lihat Detail Lengkap
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            @else
                <x-empty-state
                    title="Tidak ada aspirasi aktif"
                    description="Semua aspirasi Anda telah selesai atau belum ada yang dikirim."
                    icon="check-circle" />
            @endif
        </x-card>

        {{-- Quick info --}}
        <x-card title="Panduan Aspirasi">
            <ul class="space-y-3">
                @foreach([
                    ['icon' => 'plus-circle',    'text' => 'Jelaskan lokasi secara spesifik'],
                    ['icon' => 'eye',             'text' => 'Gunakan deskripsi yang jelas dan detail'],
                    ['icon' => 'chart-bar',       'text' => 'Sertakan foto bukti jika diperlukan'],
                    ['icon' => 'clock',           'text' => 'Pantau status melalui halaman riwayat'],
                ] as $tip)
                    <li class="flex items-start gap-3">
                        <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-500">
                            @include('layouts.icons', ['icon' => $tip['icon'], 'class' => 'h-3.5 w-3.5'])
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $tip['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
</div>
@endsection
