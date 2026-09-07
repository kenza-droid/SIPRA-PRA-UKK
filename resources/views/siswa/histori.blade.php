@extends('layouts.app')

@section('title', 'Riwayat Aspirasi')
@section('page-title', 'Riwayat Aspirasi')
@section('page-subtitle', 'Semua aspirasi yang pernah Anda kirimkan')

@section('content')

{{-- ── Header + CTA ────────────────────────────────────────────── --}}
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <p class="text-sm text-slate-500">
            Menampilkan <strong class="text-slate-700">{{ $complaints->total() }}</strong> aspirasi
        </p>
    </div>
    <a href="{{ route('siswa.complaints.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-primary-200 hover:bg-primary-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Buat Aspirasi Baru
    </a>
</div>

{{-- ── Filters ─────────────────────────────────────────────────── --}}
<x-table-filter :action="route('siswa.complaints.index')">
    {{-- Search --}}
    <div class="sm:col-span-2">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul, nomor, atau lokasi..."
                   class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        </div>
    </div>

    {{-- Status --}}
    <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Status</option>
        @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
        @endforeach
    </select>

    {{-- Kategori --}}
    <select name="category" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>

    {{-- Tanggal --}}
    <div class="flex gap-2">
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
    </div>
</x-table-filter>

{{-- ── Table / Card list ───────────────────────────────────────── --}}
@if($complaints->isEmpty())
    <div class="mt-6">
        <x-empty-state
            title="Tidak ada aspirasi ditemukan"
            description="Tidak ada aspirasi yang cocok dengan filter yang Anda pilih, atau Anda belum pernah mengirim aspirasi."
            action-route="siswa.complaints.create"
            action-label="Buat Aspirasi Pertama"
            icon="inbox-stack" />
    </div>
@else
    {{-- Desktop table --}}
    <div class="mt-6 hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 md:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/80">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Judul / Nomor</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Kategori</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Prioritas</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Status & Progress</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($complaints as $complaint)
                    <tr class="group hover:bg-primary-50/20 transition-colors">
                        {{-- Judul + Nomor digabung --}}
                        <td class="px-5 py-4 max-w-[240px]">
                            <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-primary-700">{{ $complaint->title }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                <span class="font-mono text-[10px] text-slate-400">{{ $complaint->complaint_number }}</span>
                                <span class="text-slate-200">·</span>
                                <span class="truncate text-[10px] text-slate-400">{{ $complaint->location }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full {{ $complaint->category->color_class }} px-2.5 py-1 text-xs font-medium">
                                {{ $complaint->category->name }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                            {{ $complaint->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4">
                            <x-priority-badge :priority="$complaint->priority" />
                        </td>
                        {{-- Status + progress bar digabung --}}
                        <td class="px-5 py-4 w-44">
                            <x-status-badge :status="$complaint->status" />
                            @if($complaint->progress_percentage > 0)
                                <div class="mt-2">
                                    <x-progress-bar :percentage="$complaint->progress_percentage" :show-label="false" size="sm" />
                                    <p class="mt-0.5 text-right text-[10px] font-semibold text-slate-400">{{ $complaint->progress_percentage }}%</p>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('siswa.complaints.show', $complaint) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="mt-6 space-y-3 md:hidden">
        @foreach($complaints as $complaint)
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-800">{{ $complaint->title }}</p>
                        <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $complaint->complaint_number }}</p>
                    </div>
                    <x-status-badge :status="$complaint->status" size="xs" />
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full {{ $complaint->category->color_class }} px-2 py-0.5 text-xs font-medium">{{ $complaint->category->name }}</span>
                    <x-priority-badge :priority="$complaint->priority" size="sm" />
                    <span class="text-xs text-slate-400">{{ $complaint->created_at->format('d M Y') }}</span>
                </div>
                <div class="mt-3">
                    <x-progress-bar :percentage="$complaint->progress_percentage" size="sm" />
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('siswa.complaints.show', $complaint) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $complaints->links() }}
    </div>
@endif
@endsection
