@extends('layouts.app')

@section('title', 'Manajemen Aspirasi')
@section('page-title', 'Manajemen Aspirasi')
@section('page-subtitle', 'Kelola seluruh aspirasi yang masuk dari siswa')

@section('content')

{{-- ── Summary pills ─────────────────────────────────────── --}}
<div class="mb-5 flex flex-wrap gap-2">
    @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
        <a href="{{ route('admin.complaints.index', ['status' => $key]) }}"
           class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold transition-all
               {{ request('status') === $key
                   ? 'border-primary-300 bg-primary-50 text-primary-700 shadow-sm'
                   : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
            <span class="h-1.5 w-1.5 rounded-full
                {{ match($key) {
                    'menunggu'        => 'bg-amber-400',
                    'ditinjau'        => 'bg-blue-400',
                    'diproses'        => 'bg-purple-400',
                    'dalam_perbaikan' => 'bg-orange-400',
                    'selesai'         => 'bg-emerald-400',
                    'ditolak'         => 'bg-red-400',
                    default           => 'bg-slate-400',
                } }}"></span>
            {{ $cfg['label'] }}
        </a>
    @endforeach
    @if(request('status'))
        <a href="{{ route('admin.complaints.index') }}"
           class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-red-500 transition-colors">
            × Reset
        </a>
    @endif
</div>

{{-- ── Filters ───────────────────────────────────────────── --}}
<x-table-filter :action="route('admin.complaints.index')">
    {{-- Search --}}
    <div class="sm:col-span-2 xl:col-span-2">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul, nomor, atau deskripsi..."
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

    {{-- Prioritas --}}
    <select name="priority" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Prioritas</option>
        @foreach(\App\Models\Complaint::PRIORITIES as $key => $cfg)
            <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
        @endforeach
    </select>

    {{-- Siswa --}}
    <select name="siswa" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Siswa</option>
        @foreach($siswaList as $s)
            <option value="{{ $s->id }}" {{ request('siswa') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->class }})</option>
        @endforeach
    </select>

    {{-- Tanggal --}}
    <div class="flex gap-2 sm:col-span-2">
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
    </div>

    {{-- Bulan --}}
    <select name="month" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Bulan</option>
        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $i => $m)
            <option value="{{ $i + 1 }}" {{ request('month') == $i + 1 ? 'selected' : '' }}>{{ $m }}</option>
        @endforeach
    </select>
</x-table-filter>

{{-- ── Result info --}}
<div class="mt-4 mb-3 flex items-center justify-between">
    <p class="text-sm text-slate-500">
        Menampilkan <strong class="text-slate-700">{{ $complaints->firstItem() ?? 0 }}–{{ $complaints->lastItem() ?? 0 }}</strong>
        dari <strong class="text-slate-700">{{ $complaints->total() }}</strong> aspirasi
    </p>
    <div class="flex items-center gap-2 text-xs text-slate-400">
        Urutkan:
        @foreach([['created_at','Terbaru'],['status','Status'],['priority','Prioritas']] as [$f,$l])
            <a href="{{ request()->fullUrlWithQuery(['sort' => $f, 'direction' => request('sort') === $f && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
               class="rounded-lg px-2 py-1 font-medium transition-colors {{ request('sort') === $f ? 'bg-primary-50 text-primary-600' : 'hover:bg-slate-100 text-slate-500' }}">
                {{ $l }} {{ request('sort') === $f ? (request('direction') === 'asc' ? '↑' : '↓') : '' }}
            </a>
        @endforeach
    </div>
</div>

{{-- ── Table ─────────────────────────────────────────────── --}}
@if($complaints->isEmpty())
    <x-empty-state
        title="Tidak ada aspirasi ditemukan"
        description="Coba ubah filter atau kata kunci pencarian Anda."
        icon="inbox-stack" />
@else
    {{-- Desktop table --}}
    <div class="hidden overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 lg:block">
        <table class="w-full min-w-[720px] text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/80">
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400 w-[35%]">Judul / Nomor</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400 w-[18%]">Siswa</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400 w-[14%]">Kategori</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400 w-[10%]">Tanggal</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400 w-[9%]">Prioritas</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400 w-[9%]">Status</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-400 w-[5%]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($complaints as $c)
                    <tr class="group hover:bg-primary-50/20 transition-colors">
                        {{-- Judul + nomor + lokasi --}}
                        <td class="px-3 py-3">
                            <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-primary-700 max-w-[260px]">{{ $c->title }}</p>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <span class="font-mono text-[10px] text-slate-400 whitespace-nowrap">{{ $c->complaint_number }}</span>
                                <span class="text-slate-200">·</span>
                                <span class="truncate text-[10px] text-slate-400 max-w-[120px]">{{ $c->location }}</span>
                            </div>
                        </td>
                        {{-- Siswa --}}
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ $c->user->avatar_url }}" alt="{{ $c->user->name }}"
                                     class="h-6 w-6 flex-shrink-0 rounded-full object-cover ring-1 ring-slate-100">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-semibold text-slate-700 max-w-[110px]">{{ $c->user->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $c->user->class }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Kategori --}}
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center rounded-full {{ $c->category->color_class }} px-2 py-0.5 text-[11px] font-medium whitespace-nowrap">
                                {{ $c->category->name }}
                            </span>
                        </td>
                        {{-- Tanggal --}}
                        <td class="px-3 py-3 whitespace-nowrap">
                            <p class="text-xs text-slate-600">{{ $c->created_at->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-400">{{ $c->created_at->diffForHumans() }}</p>
                        </td>
                        {{-- Prioritas --}}
                        <td class="px-3 py-3">
                            <x-priority-badge :priority="$c->priority" />
                        </td>
                        {{-- Status --}}
                        <td class="px-3 py-3">
                            <x-status-badge :status="$c->status" />
                        </td>
                        {{-- Aksi --}}
                        <td class="px-3 py-3 text-center">
                            <a href="{{ route('admin.complaints.show', $c) }}"
                               class="inline-flex items-center justify-center rounded-lg bg-primary-600 p-2 text-white hover:bg-primary-700 transition-colors shadow-sm"
                               title="Kelola Aspirasi">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="space-y-3 lg:hidden">
        @foreach($complaints as $c)
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <div class="flex items-start gap-3">
                    <div class="w-1 self-stretch flex-shrink-0 rounded-full
                        {{ match($c->priority) { 'kritis' => 'bg-red-500', 'tinggi' => 'bg-orange-400', 'sedang' => 'bg-blue-400', default => 'bg-slate-200' } }}">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $c->title }}</p>
                            <x-status-badge :status="$c->status" size="xs" />
                        </div>
                        <p class="mt-1 font-mono text-[10px] text-slate-400">{{ $c->complaint_number }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <div class="flex items-center gap-1.5">
                                <img src="{{ $c->user->avatar_url }}" class="h-5 w-5 rounded-full object-cover">
                                <span class="text-xs text-slate-500">{{ $c->user->name }}</span>
                            </div>
                            <span class="text-slate-200">·</span>
                            <span class="text-xs text-slate-400">{{ $c->category->name }}</span>
                            <span class="text-slate-200">·</span>
                            <span class="text-xs text-slate-400">{{ $c->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <x-priority-badge :priority="$c->priority" />
                            <a href="{{ route('admin.complaints.show', $c) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-primary-700">
                                Kelola →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">{{ $complaints->links() }}</div>
@endif

@endsection
