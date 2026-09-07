@extends('layouts.app')

@section('title', $complaint->title)
@section('page-title', 'Detail Aspirasi')
@section('page-subtitle', $complaint->complaint_number)

@section('content')

{{-- Back + header --}}
<x-page-header
    title="{{ $complaint->title }}"
    subtitle="Dikirim {{ $complaint->created_at->format('d M Y, H:i') }} · {{ $complaint->days_open }} hari lalu"
    back-route="siswa.complaints.index"
    back-label="Kembali ke Riwayat">
    <x-status-badge :status="$complaint->status" size="lg" />
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- ── Kolom Kiri: Detail Aspirasi ───────────────────────── --}}
    <div class="space-y-6 lg:col-span-2">

        {{-- Info cards row --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach([
                ['label' => 'Nomor',    'value' => $complaint->complaint_number, 'mono' => true],
                ['label' => 'Kategori', 'value' => $complaint->category->name,   'mono' => false],
                ['label' => 'Prioritas','value' => $complaint->priority_label,   'mono' => false],
                ['label' => 'Lokasi',   'value' => $complaint->location,         'mono' => false],
            ] as $info)
                <div class="rounded-xl bg-white p-3.5 shadow-sm ring-1 ring-slate-100">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $info['label'] }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800 {{ $info['mono'] ? 'font-mono' : '' }} truncate">{{ $info['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Deskripsi --}}
        <x-card title="Deskripsi Aspirasi">
            <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                {!! nl2br(e($complaint->description)) !!}
            </div>
        </x-card>

        {{-- Foto Bukti --}}
        @if($complaint->images->isNotEmpty())
            <x-card title="Foto Bukti" :subtitle="$complaint->images->count() . ' foto dilampirkan'">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach($complaint->images as $img)
                        <a href="{{ $img->url }}" target="_blank"
                           class="group relative aspect-video overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 hover:ring-primary-300 transition-all">
                            <img src="{{ $img->url }}"
                                 alt="{{ $img->original_name }}"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition-colors group-hover:bg-black/20">
                                <svg class="h-8 w-8 text-white opacity-0 drop-shadow-lg transition-opacity group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Umpan Balik Admin --}}
        <x-card title="Umpan Balik Admin" :subtitle="$complaint->feedback->count() . ' umpan balik'">
            @if($complaint->feedback->isEmpty())
                <div class="flex flex-col items-center py-8 text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-3">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">Belum ada umpan balik</p>
                    <p class="mt-1 text-xs text-slate-400">Admin akan memberikan respons setelah meninjau aspirasi Anda.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($complaint->feedback as $fb)
                        <div class="rounded-xl border {{ $fb->type_badge_class }} p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $fb->admin->avatar_url }}" alt="{{ $fb->admin->name }}"
                                         class="h-8 w-8 rounded-full object-cover ring-2 ring-white shadow-sm">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $fb->admin->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $fb->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $fb->type_badge_class }}">
                                    {{ $fb->type_config['label'] }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-slate-700 leading-relaxed">{{ $fb->message }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        {{-- Histori Aktivitas --}}
        <x-card title="Histori Aktivitas" :subtitle="$complaint->history->count() . ' catatan perubahan'">
            <div class="space-y-0">
                @foreach($complaint->history as $h)
                    <div class="flex gap-3 py-3 border-b border-slate-50 last:border-0">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full
                                {{ match($h->action_color) {
                                    'indigo'  => 'bg-indigo-100 text-indigo-600',
                                    'blue'    => 'bg-blue-100 text-blue-600',
                                    'purple'  => 'bg-purple-100 text-purple-600',
                                    'emerald' => 'bg-emerald-100 text-emerald-600',
                                    'amber'   => 'bg-amber-100 text-amber-600',
                                    default   => 'bg-slate-100 text-slate-500',
                                } }}">
                                @include('layouts.icons', ['icon' => $h->action_icon, 'class' => 'h-3.5 w-3.5'])
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-slate-700">{{ $h->action_label }}</p>
                                @if($h->previous_status && $h->new_status && $h->previous_status !== $h->new_status)
                                    <span class="flex items-center gap-1 text-xs text-slate-400">
                                        <x-status-badge :status="$h->previous_status" size="xs" />
                                        <svg class="h-3 w-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        <x-status-badge :status="$h->new_status" size="xs" />
                                    </span>
                                @endif
                            </div>
                            @if($h->note)
                                <p class="mt-0.5 text-xs text-slate-500">{{ $h->note }}</p>
                            @endif
                            <p class="mt-1 text-xs text-slate-400">
                                {{ $h->changedBy->name }} · {{ $h->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    {{-- ── Kolom Kanan: Status & Progress ────────────────────── --}}
    <div class="space-y-6">

        {{-- Status card --}}
        <x-card title="Status Penyelesaian">
            <div class="mb-4">
                <x-progress-bar :percentage="$complaint->progress_percentage" size="lg" />
            </div>
            <x-timeline :steps="$complaint->progress_steps" />
        </x-card>

        {{-- Progress history --}}
        @if($complaint->progress->isNotEmpty())
            <x-card title="Log Progres Perbaikan" :subtitle="$complaint->progress->count() . ' update'">
                <div class="space-y-4">
                    @foreach($complaint->progress as $prog)
                        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-800">{{ $prog->title }}</p>
                                <span class="flex-shrink-0 text-lg font-black {{ $prog->progress_bar_color === 'bg-emerald-500' ? 'text-emerald-600' : ($prog->progress_bar_color === 'bg-blue-500' ? 'text-blue-600' : 'text-indigo-600') }}">
                                    {{ $prog->progress_percentage }}%
                                </span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">{{ $prog->description }}</p>
                            <div class="mt-2.5">
                                <x-progress-bar :percentage="$prog->progress_percentage" :show-label="false" size="sm" />
                            </div>
                            <p class="mt-2 text-xs text-slate-400">
                                {{ $prog->creator->name }}
                                @if($prog->work_date) · {{ $prog->work_date->format('d M Y') }} @endif
                                · {{ $prog->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Info aspirasi --}}
        <x-card title="Informasi Aspirasi">
            <dl class="space-y-3">
                @foreach([
                    ['label' => 'Nomor Aspirasi', 'value' => $complaint->complaint_number, 'mono' => true],
                    ['label' => 'Tanggal Kirim',  'value' => $complaint->created_at->format('d M Y, H:i')],
                    ['label' => 'Kategori',        'value' => $complaint->category->name],
                    ['label' => 'Lokasi',          'value' => $complaint->location],
                    ['label' => 'Prioritas',       'value' => $complaint->priority_label],
                ] as $dl)
                    <div class="flex items-start justify-between gap-2">
                        <dt class="text-xs text-slate-400 flex-shrink-0">{{ $dl['label'] }}</dt>
                        <dd class="text-xs font-semibold text-slate-700 text-right {{ ($dl['mono'] ?? false) ? 'font-mono' : '' }}">
                            {{ $dl['value'] }}
                        </dd>
                    </div>
                @endforeach
                @if($complaint->resolved_at)
                    <div class="flex items-start justify-between gap-2">
                        <dt class="text-xs text-slate-400 flex-shrink-0">Tanggal Selesai</dt>
                        <dd class="text-xs font-semibold text-emerald-600 text-right">{{ $complaint->resolved_at->format('d M Y') }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>
    </div>
</div>
@endsection
