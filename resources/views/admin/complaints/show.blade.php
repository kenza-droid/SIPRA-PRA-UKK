@extends('layouts.app')

@section('title', 'Kelola Aspirasi')
@section('page-title', 'Kelola Aspirasi')
@section('page-subtitle', $complaint->complaint_number)

@section('content')

{{-- Back + header --}}
<x-page-header
    title="{{ $complaint->title }}"
    subtitle="Dikirim oleh {{ $complaint->user->name }} · {{ $complaint->created_at->format('d M Y, H:i') }}"
    back-route="admin.complaints.index"
    back-label="Kembali">
    <x-status-badge :status="$complaint->status" size="lg" />
    <x-priority-badge :priority="$complaint->priority" size="lg" />
</x-page-header>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- ══════════════════════════════════════════════════════════
         KOLOM KIRI — Informasi Aspirasi
    ══════════════════════════════════════════════════════════ --}}
    <div class="space-y-6 xl:col-span-2">

        {{-- Info summary chips --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach([
                ['label' => 'Siswa',    'value' => $complaint->user->name,     'sub' => $complaint->user->class],
                ['label' => 'NISN',     'value' => $complaint->user->nisn ?? '—', 'mono' => true],
                ['label' => 'Kategori', 'value' => $complaint->category->name],
                ['label' => 'Lokasi',   'value' => $complaint->location],
            ] as $info)
                <div class="rounded-xl bg-white p-3.5 shadow-sm ring-1 ring-slate-100">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $info['label'] }}</p>
                    <p class="mt-1 truncate text-sm font-semibold text-slate-800 {{ ($info['mono'] ?? false) ? 'font-mono' : '' }}">{{ $info['value'] }}</p>
                    @if(isset($info['sub']))
                        <p class="text-xs text-slate-400 truncate">{{ $info['sub'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Deskripsi --}}
        <x-card title="Deskripsi Aspirasi">
            <div class="text-sm leading-relaxed text-slate-700">
                {!! nl2br(e($complaint->description)) !!}
            </div>
        </x-card>

        {{-- Foto --}}
        @if($complaint->images->isNotEmpty())
            <x-card title="Foto Bukti" :subtitle="$complaint->images->count() . ' foto'">
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                    @foreach($complaint->images as $img)
                        <a href="{{ $img->url }}" target="_blank"
                           class="group relative aspect-video overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 hover:ring-primary-300 transition-all">
                            <img src="{{ $img->url }}" alt="{{ $img->original_name }}"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-colors">
                                <svg class="h-7 w-7 text-white opacity-0 drop-shadow-lg group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Umpan balik history --}}
        <x-card title="Riwayat Umpan Balik" :subtitle="$complaint->feedback->count() . ' pesan'">
            @if($complaint->feedback->isEmpty())
                <p class="py-4 text-center text-sm text-slate-400">Belum ada umpan balik diberikan.</p>
            @else
                <div class="space-y-3">
                    @foreach($complaint->feedback as $fb)
                        <div class="rounded-xl border border-slate-100 p-4">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $fb->admin->avatar_url }}" class="h-7 w-7 rounded-full object-cover ring-2 ring-white shadow-sm">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-700">{{ $fb->admin->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $fb->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $fb->type_badge_class }}">
                                    {{ $fb->type_config['label'] }}
                                </span>
                            </div>
                            <p class="mt-2.5 text-sm text-slate-700 leading-relaxed">{{ $fb->message }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        {{-- Progress log --}}
        @if($complaint->progress->isNotEmpty())
            <x-card title="Log Progres Perbaikan">
                <div class="space-y-4">
                    @foreach($complaint->progress as $p)
                        <div class="flex gap-4 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-white font-black text-sm ring-1 ring-slate-200
                                {{ match(true) {
                                    $p->progress_percentage >= 100 => 'text-emerald-600',
                                    $p->progress_percentage >= 50  => 'text-blue-600',
                                    default                         => 'text-indigo-600',
                                } }}">
                                {{ $p->progress_percentage }}%
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800">{{ $p->title }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 leading-relaxed">{{ $p->description }}</p>
                                <div class="mt-2">
                                    <x-progress-bar :percentage="$p->progress_percentage" :show-label="false" size="sm" />
                                </div>
                                <p class="mt-1.5 text-[10px] text-slate-400">
                                    {{ $p->creator->name }}
                                    @if($p->work_date) · {{ $p->work_date->format('d M Y') }} @endif
                                    · {{ $p->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Histori --}}
        <x-card title="Histori Aktivitas" :subtitle="$complaint->history->count() . ' catatan'">
            <div class="space-y-0 divide-y divide-slate-50">
                @foreach($complaint->history as $h)
                    <div class="flex gap-3 py-3">
                        <div class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full
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
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <p class="text-sm font-semibold text-slate-700">{{ $h->action_label }}</p>
                                @if($h->previous_status && $h->new_status && $h->previous_status !== $h->new_status)
                                    <x-status-badge :status="$h->previous_status" size="xs" />
                                    <svg class="h-3 w-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                    <x-status-badge :status="$h->new_status" size="xs" />
                                @endif
                            </div>
                            @if($h->note)
                                <p class="mt-0.5 text-xs text-slate-500">{{ $h->note }}</p>
                            @endif
                            <p class="mt-1 text-[10px] text-slate-400">{{ $h->changedBy->name }} · {{ $h->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         KOLOM KANAN — Unified Management Panel
    ══════════════════════════════════════════════════════════ --}}
    <div class="space-y-5"
         x-data="{
             selectedStatus: '{{ old('status', $complaint->status) }}',
             pct: {{ old('progress_percentage', $complaint->progress_percentage) }},
             message: '{{ old('message', '') }}',
             feedbackType: '{{ old('feedback_type', 'informasi') }}',
             msgLen: {{ strlen(old('message', '')) }},
             showDoc: {{ old('title') || old('image') ? 'true' : 'false' }},
             onStatusChange(val) {
                 this.selectedStatus = val;
                 if (val === 'selesai') {
                     this.pct = 100;
                     this.feedbackType = 'selesai';
                 } else if (val === 'dalam_perbaikan') {
                     if (this.pct < 50) this.pct = 50;
                     this.feedbackType = 'tindakan';
                 } else if (val === 'diproses') {
                     if (this.pct < 25) this.pct = 25;
                     this.feedbackType = 'tindakan';
                 } else if (val === 'ditolak') {
                     this.feedbackType = 'penolakan';
                 } else if (val === 'ditinjau') {
                     this.feedbackType = 'informasi';
                 }
             }
         }">

        <x-card title="Tindak Lanjut Aspirasi" subtitle="Perbarui status, progres, dan umpan balik siswa dalam satu langkah">
            <form method="POST" action="{{ route('admin.complaints.action', $complaint) }}" enctype="multipart/form-data">
                @csrf

                {{-- 1. Pilihan Status --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-700">
                            1. Status Aspirasi <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Pilih status terbaru</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
                            <label class="flex cursor-pointer items-center justify-between rounded-xl border-2 p-2.5 transition-all text-left"
                                   :class="selectedStatus === '{{ $key }}'
                                       ? 'border-primary-500 bg-primary-50/70 shadow-sm ring-1 ring-primary-500'
                                       : 'border-slate-100 hover:border-slate-200 bg-white'">
                                <div class="flex items-center gap-2 min-w-0">
                                    <input type="radio" name="status" value="{{ $key }}"
                                           @change="onStatusChange('{{ $key }}')"
                                           :checked="selectedStatus === '{{ $key }}'"
                                           class="text-primary-600 focus:ring-primary-400">
                                    <span class="truncate text-xs font-semibold
                                        {{ match($key) {
                                            'menunggu'        => 'text-amber-700',
                                            'ditinjau'        => 'text-sky-700',
                                            'diproses'        => 'text-purple-700',
                                            'dalam_perbaikan' => 'text-orange-700',
                                            'selesai'         => 'text-emerald-700',
                                            'ditolak'         => 'text-red-700',
                                            default           => 'text-slate-700',
                                        } }}">{{ $cfg['label'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Persentase Progres --}}
                <div class="mb-5 rounded-xl border border-slate-100 bg-slate-50/60 p-3.5">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-700">
                            2. Persentase Progres
                        </label>
                        <div class="flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 px-2 py-0.5 shadow-2xs">
                            <input type="number" name="progress_percentage" x-model="pct"
                                   min="0" max="100"
                                   class="w-10 bg-transparent text-center text-xs font-bold text-primary-700 focus:outline-none">
                            <span class="text-xs font-bold text-primary-600">%</span>
                        </div>
                    </div>
                    <input type="range" x-model="pct" min="0" max="100" step="5"
                           class="w-full accent-primary-600 cursor-pointer">
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-primary-600 transition-all duration-200"
                             :style="'width:' + pct + '%'"></div>
                    </div>
                    <div class="mt-1.5 flex justify-between text-[10px] text-slate-400">
                        <span>0% (Awal)</span>
                        <span>50% (Pengerjaan)</span>
                        <span>100% (Selesai)</span>
                    </div>
                </div>

                {{-- 3. Umpan Balik / Catatan untuk Siswa --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-700">
                            3. Pesan & Umpan Balik ke Siswa
                        </label>
                        <span class="text-[11px] text-slate-400">Opsional / Disarankan</span>
                    </div>

                    {{-- Pilihan Tipe Umpan Balik --}}
                    <div class="mb-2 flex flex-wrap gap-1.5">
                        @foreach(\App\Models\Feedback::TYPES as $key => $cfg)
                            <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs transition-colors"
                                   :class="feedbackType === '{{ $key }}' ? 'border-primary-500 bg-primary-50 text-primary-700 font-semibold' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                                <input type="radio" name="feedback_type" value="{{ $key }}"
                                       x-model="feedbackType" class="sr-only">
                                <span>{{ $cfg['label'] }}</span>
                            </label>
                        @endforeach
                    </div>

                    <textarea name="message" rows="3" maxlength="1000"
                              x-model="message"
                              @input="msgLen = $event.target.value.length"
                              placeholder="Tuliskan catatan, tanggapan, atau perkembangan yang akan dibaca oleh siswa..."
                              class="w-full resize-none rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100"></textarea>
                    <div class="mt-1 flex justify-end">
                        <span class="text-[11px] text-slate-400" x-text="msgLen + '/1000'"></span>
                    </div>
                </div>

                {{-- 4. Dokumentasi & Foto Bukti (Opsional / Collapse) --}}
                <div class="mb-5 rounded-xl border border-slate-200/80 bg-white p-3.5">
                    <button type="button" @click="showDoc = !showDoc"
                            class="flex w-full items-center justify-between text-left">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                            </svg>
                            Foto &amp; Dokumentasi Perbaikan
                            <span class="text-[10px] font-normal normal-case text-slate-400">(opsional)</span>
                        </span>
                        <svg class="h-4 w-4 text-slate-400 transition-transform duration-200"
                             :class="showDoc ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>

                    <div x-show="showDoc" x-cloak class="mt-3 space-y-3 pt-3 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Judul Kegiatan / Progres</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                   placeholder="cth: Penggantian komponen proyektor selesai"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-primary-400 focus:bg-white focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Pengerjaan</label>
                                <input type="date" name="work_date" value="{{ old('work_date', today()->format('Y-m-d')) }}"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-primary-400 focus:bg-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Foto Hasil / Bukti</label>
                                <input type="file" name="image" accept="image/*"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs file:mr-2 file:rounded-md file:border-0 file:bg-primary-50 file:px-2 file:py-1 file:text-[11px] file:font-semibold file:text-primary-700 hover:file:bg-primary-100 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Simpan Terpadu --}}
                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-3 text-sm font-bold text-white shadow-md shadow-primary-200 hover:bg-primary-700 active:scale-[0.99] transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Simpan &amp; Perbarui Aspirasi
                </button>
            </form>
        </x-card>

        {{-- Progress summary --}}
        <x-card title="Progress Saat Ini">
            <x-progress-bar :percentage="$complaint->progress_percentage" size="lg" />
            <x-timeline :steps="$complaint->progress_steps" />
        </x-card>

        {{-- Quick info --}}
        <x-card title="Informasi Aspirasi">
            <dl class="space-y-2.5">
                @foreach([
                    ['label' => 'Nomor',        'value' => $complaint->complaint_number, 'mono' => true],
                    ['label' => 'Tanggal Kirim', 'value' => $complaint->created_at->format('d M Y, H:i')],
                    ['label' => 'Ditinjau',      'value' => $complaint->reviewed_at?->format('d M Y') ?? '—'],
                    ['label' => 'Diselesaikan',  'value' => $complaint->resolved_at?->format('d M Y') ?? '—'],
                    ['label' => 'Hari Terbuka',  'value' => $complaint->days_open . ' hari'],
                ] as $dl)
                    <div class="flex items-start justify-between gap-2">
                        <dt class="text-xs text-slate-400 flex-shrink-0">{{ $dl['label'] }}</dt>
                        <dd class="text-xs font-semibold text-slate-700 text-right {{ ($dl['mono'] ?? false) ? 'font-mono' : '' }}">
                            {{ $dl['value'] }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </x-card>
    </div>
</div>
@endsection
