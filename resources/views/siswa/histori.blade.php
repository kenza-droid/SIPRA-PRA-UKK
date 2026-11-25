@extends('layouts.app')

@section('title', 'Riwayat Aspirasi')
@section('page-title', 'Riwayat Aspirasi')
@section('page-subtitle', 'Semua aspirasi yang pernah Anda kirimkan')

@section('content')

@php
    $allowedStatuses = ['menunggu', 'selesai', 'ditolak'];
    $deletableIds = $complaints->filter(fn($c) => in_array($c->status, $allowedStatuses))->pluck('id')->values()->all();
@endphp

<div x-data="{
    selected: [],
    deleting: false,
    deletableIds: {{ json_encode($deletableIds) }},
    toggleAll() {
        if (this.isAllSelected) {
            this.selected = [];
        } else {
            this.selected = [...this.deletableIds];
        }
    },
    get isAllSelected() {
        return this.deletableIds.length > 0 && this.selected.length === this.deletableIds.length;
    }
}">

    {{-- ── Header + CTA ────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">
                Menampilkan <strong class="text-slate-700">{{ $complaints->total() }}</strong> aspirasi
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Tombol Aksi Hapus Terpilih Dinamis di Header --}}
            <button type="button"
                    x-show="selected.length > 0"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150 motion-reduce:transition-none"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100 motion-reduce:transition-none"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click="$dispatch('open-modal', { id: 'modal-bulk-delete' })"
                    class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-rose-200 hover:bg-rose-700 active:scale-[0.98] transition-all duration-150 motion-reduce:transition-none motion-reduce:transform-none">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                <span x-text="'Hapus Terpilih (' + selected.length + ')'">Hapus Terpilih</span>
            </button>

            <a href="{{ route('siswa.complaints.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-primary-200 hover:bg-primary-700 active:scale-[0.98] transition-all duration-150 motion-reduce:transition-none motion-reduce:transform-none">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Buat Aspirasi Baru
            </a>
        </div>
    </div>

    {{-- ── Filters ─────────────────────────────────────────────────── --}}
    <x-table-filter :action="route('siswa.complaints.index')">
        {{-- Search --}}
        <div class="sm:col-span-2 min-w-0">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul, nomor, atau lokasi..."
                       class="w-full min-w-0 rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
            </div>
        </div>

        {{-- Status --}}
        <div class="min-w-0">
            <select name="status" aria-label="Filter status" class="w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                <option value="">Semua Status</option>
                @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Kategori --}}
        <div class="min-w-0">
            <select name="category" aria-label="Filter kategori" class="w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tanggal (Responsif Grid 2 Kolom Bebas Overflow) --}}
        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-2 min-w-0">
            <input type="date" name="date_from" value="{{ request('date_from') }}" aria-label="Dari tanggal"
                   class="w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
            <input type="date" name="date_to" value="{{ request('date_to') }}" aria-label="Sampai tanggal"
                   class="w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        </div>
    </x-table-filter>

    {{-- ── Banner Aksi Seleksi Massal (Floating / Sticky Context) ── --}}
    <div x-show="selected.length > 0"
         x-cloak
         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-rose-50/90 border border-rose-200/80 p-3.5 text-rose-900 shadow-xs">
        <div class="flex items-center gap-2.5">
            <span class="flex h-6 min-w-[24px] items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white" x-text="selected.length"></span>
            <span class="text-sm font-semibold text-rose-900">Aspirasi Terpilih</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    @click="selected = []"
                    class="rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-white/80 active:scale-[0.98] transition-all motion-reduce:transition-none">
                Batal Pilihan
            </button>
            <button type="button"
                    @click="$dispatch('open-modal', { id: 'modal-bulk-delete' })"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-rose-700 active:scale-[0.98] transition-all duration-150 motion-reduce:transition-none">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                <span x-text="'Hapus Terpilih (' + selected.length + ')'">Hapus Terpilih</span>
            </button>
        </div>
    </div>

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
        <div class="mt-6 hidden overflow-hidden rounded-2xl bg-white border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)] md:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        {{-- Checkbox Header Pilih Semua --}}
                        <th class="w-12 px-4 py-3.5 text-center">
                            <label class="inline-flex min-h-[28px] min-w-[28px] items-center justify-center cursor-pointer">
                                <input type="checkbox"
                                       @change="toggleAll()"
                                       :checked="isAllSelected"
                                       :indeterminate.prop="selected.length > 0 && !isAllSelected"
                                       :disabled="deletableIds.length === 0"
                                       aria-label="Pilih semua aspirasi"
                                       class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                            </label>
                        </th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Judul / Nomor</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Kategori</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Prioritas</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Status & Progres</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    @foreach($complaints as $complaint)
                        @php
                            $isDeletable = in_array($complaint->status, $allowedStatuses);
                        @endphp
                        <tr class="group hover:bg-slate-50/80 transition-colors duration-150">
                            {{-- Checkbox Baris --}}
                            <td class="w-12 px-4 py-4 text-center">
                                @if($isDeletable)
                                    <label class="inline-flex min-h-[28px] min-w-[28px] items-center justify-center cursor-pointer">
                                        <input type="checkbox"
                                               :value="{{ $complaint->id }}"
                                               x-model="selected"
                                               aria-label="Pilih aspirasi nomor {{ $complaint->complaint_number }}"
                                               class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                                    </label>
                                @else
                                    <span title="Status {{ $complaint->status_label }} tidak dapat dihapus saat dalam penanganan" class="inline-flex min-h-[28px] min-w-[28px] items-center justify-center">
                                        <input type="checkbox" disabled aria-label="Tidak dapat dihapus" class="h-4 w-4 rounded border-slate-200 bg-slate-100 text-slate-300 cursor-not-allowed">
                                    </span>
                                @endif
                            </td>

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
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 active:scale-[0.98] transition-all duration-150">
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
                @php
                    $isDeletable = in_array($complaint->status, $allowedStatuses);
                @endphp
                <div class="rounded-2xl bg-white p-4 border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300/80 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            {{-- Checkbox Mobile --}}
                            <div class="pt-0.5 flex-shrink-0">
                                @if($isDeletable)
                                    <label class="inline-flex min-h-[32px] min-w-[32px] items-center justify-center cursor-pointer">
                                        <input type="checkbox"
                                               :value="{{ $complaint->id }}"
                                               x-model="selected"
                                               aria-label="Pilih aspirasi nomor {{ $complaint->complaint_number }}"
                                               class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                                    </label>
                                @else
                                    <span title="Status {{ $complaint->status_label }} tidak dapat dihapus saat dalam penanganan" class="inline-flex min-h-[32px] min-w-[32px] items-center justify-center">
                                        <input type="checkbox" disabled aria-label="Tidak dapat dihapus" class="h-4 w-4 rounded border-slate-200 bg-slate-100 text-slate-300 cursor-not-allowed">
                                    </span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-800">{{ $complaint->title }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $complaint->complaint_number }}</p>
                            </div>
                        </div>
                        <x-status-badge :status="$complaint->status" size="xs" />
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full {{ $complaint->category->color_class }} px-2 py-0.5 text-xs font-medium">{{ $complaint->category->name }}</span>
                        <x-priority-badge :priority="$complaint->priority" size="sm" />
                        <span class="text-xs text-slate-400">{{ $complaint->created_at->format('d M Y') }}</span>
                    </div>
                    @if($complaint->progress_percentage > 0)
                        <div class="mt-3">
                            <x-progress-bar :percentage="$complaint->progress_percentage" size="sm" />
                        </div>
                    @endif
                    <div class="mt-3 flex justify-end">
                        <a href="{{ route('siswa.complaints.show', $complaint) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100 active:scale-[0.98] transition-all">
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

    {{-- ── Modal Konfirmasi Hapus Menggunakan <x-modal> ────────────── --}}
    <x-modal id="modal-bulk-delete" title="Konfirmasi Hapus Aspirasi Terpilih" size="md">
        <div class="space-y-3">
            <div class="flex items-start gap-3 rounded-xl bg-rose-50/80 border border-rose-200/60 p-3 text-rose-800">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold">Tindakan ini permanen</p>
                    <p class="text-xs text-rose-700/90 leading-relaxed">Data riwayat pengaduan yang dihapus beserta lampiran gambarnya tidak dapat dikembalikan lagi.</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">
                Apakah Anda yakin ingin menghapus <strong class="font-bold text-slate-900" x-text="selected.length"></strong> aspirasi yang telah dicentang?
            </p>
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        :disabled="deleting"
                        @click="$dispatch('close-modal', { id: 'modal-bulk-delete' })"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-all motion-reduce:transition-none motion-reduce:transform-none disabled:opacity-50">
                    Batal
                </button>
                <button type="submit"
                        form="form-bulk-delete"
                        :disabled="deleting || selected.length === 0"
                        class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-rose-700 active:scale-[0.98] transition-all duration-150 motion-reduce:transition-none motion-reduce:transform-none disabled:opacity-70">
                    <svg x-show="deleting" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <svg x-show="!deleting" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus'">Ya, Hapus</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Form DELETE Rahasia untuk Bulk Action --}}
    <form id="form-bulk-delete"
          method="POST"
          action="{{ route('siswa.complaints.bulk-destroy') }}"
          @submit="deleting = true"
          class="hidden">
        @csrf
        @method('DELETE')
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

</div>
@endsection
