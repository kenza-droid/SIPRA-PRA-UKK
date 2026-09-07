@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Daftar seluruh siswa terdaftar di SIPRA')

@section('content')

{{-- ── Filter ──────────────────────────────────────────────── --}}
<x-table-filter :action="route('admin.siswa')">
    <div class="sm:col-span-2">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, NISN, atau kelas..."
                   class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        </div>
    </div>
</x-table-filter>

{{-- ── Result info ─────────────────────────────────────────── --}}
<div class="mt-4 mb-3">
    <p class="text-sm text-slate-500">
        Menampilkan <strong class="text-slate-700">{{ $siswaList->total() }}</strong> siswa
    </p>
</div>

@if($siswaList->isEmpty())
    <x-empty-state
        title="Tidak ada siswa ditemukan"
        description="Coba ubah kata kunci pencarian Anda."
        icon="users" />
@else
    {{-- Desktop table --}}
    <div class="hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 md:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/80">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">#</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Siswa</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">NISN</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Kelas</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Email</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-400">Total Aspirasi</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-400">Selesai</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($siswaList as $i => $siswa)
                    <tr class="group hover:bg-primary-50/20 transition-colors">
                        <td class="px-5 py-4 text-xs text-slate-400">
                            {{ $siswaList->firstItem() + $i }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $siswa->avatar_url }}" alt="{{ $siswa->name }}"
                                     class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-100">
                                <p class="text-sm font-semibold text-slate-800">{{ $siswa->name }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs text-slate-600">{{ $siswa->nisn ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $siswa->class ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500">
                            {{ $siswa->email }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($siswa->complaints_count > 0)
                                <span class="inline-flex items-center justify-center rounded-full bg-primary-100 px-2.5 py-1 text-xs font-bold text-primary-700">
                                    {{ $siswa->complaints_count }}
                                </span>
                            @else
                                <span class="text-xs text-slate-300">0</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($siswa->selesai_count > 0)
                                <span class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                    {{ $siswa->selesai_count }}
                                </span>
                            @else
                                <span class="text-xs text-slate-300">0</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('admin.complaints.index', ['siswa' => $siswa->id]) }}"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                Lihat Aspirasi
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="space-y-3 md:hidden">
        @foreach($siswaList as $siswa)
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <div class="flex items-center gap-3">
                    <img src="{{ $siswa->avatar_url }}" alt="{{ $siswa->name }}"
                         class="h-11 w-11 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-100">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-slate-800">{{ $siswa->name }}</p>
                        <p class="text-xs text-slate-400">{{ $siswa->class ?? '—' }}</p>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-xl bg-slate-50 p-2">
                        <p class="text-lg font-black text-slate-700">{{ $siswa->complaints_count }}</p>
                        <p class="text-[10px] text-slate-400">Total</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-2">
                        <p class="text-lg font-black text-emerald-600">{{ $siswa->selesai_count }}</p>
                        <p class="text-[10px] text-slate-400">Selesai</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-2">
                        <p class="truncate text-xs font-bold text-slate-600 pt-1">{{ $siswa->nisn ?? '—' }}</p>
                        <p class="text-[10px] text-slate-400">NISN</p>
                    </div>
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('admin.complaints.index', ['siswa' => $siswa->id]) }}"
                       class="inline-flex items-center gap-1 rounded-xl bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100 transition-colors">
                        Lihat Aspirasi →
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">{{ $siswaList->links() }}</div>
@endif

@endsection
