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
         KOLOM KANAN — Management Panel
    ══════════════════════════════════════════════════════════ --}}
    <div class="space-y-5" x-data="{ activePanel: 'status' }">

        {{-- Panel tabs --}}
        <div class="flex gap-1 rounded-xl bg-slate-100 p-1">
            @foreach([['status','Update Status'],['feedback','Umpan Balik'],['progress','Tambah Progres']] as [$tab,$label])
                <button type="button"
                        @click="activePanel = '{{ $tab }}'"
                        :class="activePanel === '{{ $tab }}' ? 'bg-white text-primary-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="flex-1 rounded-lg py-2 text-xs font-semibold transition-all">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- ── Panel: Update Status ─────────────────────────── --}}
        <div x-show="activePanel === 'status'" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">
            <x-card title="Update Status Aspirasi">
                <form method="POST" action="{{ route('admin.complaints.status', $complaint) }}"
                      x-data="{ selectedStatus: '{{ $complaint->status }}' }">
                    @csrf
                    @method('PUT')

                    {{-- Current status indicator --}}
                    <div class="mb-4 flex items-center gap-2 rounded-xl bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Status saat ini:</p>
                        <x-status-badge :status="$complaint->status" />
                    </div>

                    <div class="mb-4 space-y-2">
                        @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-3.5 transition-all"
                                   :class="selectedStatus === '{{ $key }}'
                                       ? 'border-primary-400 bg-primary-50/60'
                                       : 'border-slate-100 hover:border-slate-200 bg-white'">
                                <input type="radio" name="status" value="{{ $key }}"
                                       x-model="selectedStatus"
                                       class="text-primary-600 focus:ring-primary-400">
                                <span class="flex-1">
                                    <x-status-badge :status="$key" />
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <x-form-input name="note" label="Catatan (opsional)">
                        <textarea name="note" rows="2" maxlength="500"
                                  placeholder="Tambahkan catatan perubahan status..."
                                  class="w-full resize-none rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100">{{ old('note') }}</textarea>
                    </x-form-input>

                    <button type="submit"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700 transition-colors">
                        @include('layouts.icons', ['icon' => 'arrow-path', 'class' => 'h-4 w-4'])
                        Perbarui Status
                    </button>
                </form>
            </x-card>
        </div>

        {{-- ── Panel: Umpan Balik ───────────────────────────── --}}
        <div x-show="activePanel === 'feedback'" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">
            <x-card title="Berikan Umpan Balik">
                <form method="POST" action="{{ route('admin.complaints.feedback', $complaint) }}"
                      x-data="{ msgLen: 0 }">
                    @csrf

                    <div class="mb-4 space-y-2">
                        <p class="text-xs font-medium text-slate-600 mb-1.5">Tipe Umpan Balik</p>
                        @foreach(\App\Models\Feedback::TYPES as $key => $cfg)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-[1.5px] border-slate-100 p-3 hover:border-slate-200 transition-colors">
                                <input type="radio" name="type" value="{{ $key }}"
                                       {{ old('type', 'informasi') === $key ? 'checked' : '' }}
                                       class="text-primary-600 focus:ring-primary-400">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ match($key) {
                                        'informasi' => 'bg-blue-100 text-blue-700',
                                        'tindakan'  => 'bg-green-100 text-green-700',
                                        'penolakan' => 'bg-red-100 text-red-700',
                                        'selesai'   => 'bg-emerald-100 text-emerald-700',
                                        default     => 'bg-gray-100 text-gray-700',
                                    } }}">{{ $cfg['label'] }}</span>
                                <span class="text-xs text-slate-500">{{ match($key) {
                                    'informasi' => 'Memberikan informasi tambahan',
                                    'tindakan'  => 'Menyatakan tindakan yang diambil',
                                    'penolakan' => 'Menolak atau tidak bisa ditangani',
                                    'selesai'   => 'Konfirmasi selesai',
                                    default     => '',
                                } }}</span>
                            </label>
                        @endforeach
                    </div>

                    <x-form-input name="message" label="Isi Umpan Balik" :required="true">
                        <textarea name="message" rows="5" maxlength="1000"
                                  @input="msgLen = $event.target.value.length"
                                  placeholder="Tuliskan umpan balik yang jelas dan informatif untuk siswa..."
                                  class="w-full resize-none rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                        <div class="mt-1 flex justify-end">
                            <span class="text-xs text-slate-400" x-text="msgLen + '/1000'"></span>
                        </div>
                    </x-form-input>

                    <button type="submit"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700 transition-colors">
                        @include('layouts.icons', ['icon' => 'chat-bubble-left-right', 'class' => 'h-4 w-4'])
                        Kirim Umpan Balik
                    </button>
                </form>
            </x-card>
        </div>

        {{-- ── Panel: Tambah Progres ────────────────────────── --}}
        <div x-show="activePanel === 'progress'" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">
            <x-card title="Tambah Progres Perbaikan">
                <form method="POST" action="{{ route('admin.complaints.progress', $complaint) }}"
                      enctype="multipart/form-data"
                      x-data="{ pct: {{ $complaint->progress_percentage }}, descLen: 0 }">
                    @csrf

                    <div class="mb-4 space-y-4">

                        <x-form-input name="title" label="Judul Progres" :required="true">
                            <input type="text" name="title" value="{{ old('title') }}"
                                   placeholder="cth: Pengecekan Lapangan Selesai"
                                   class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('title') border-red-400 @enderror">
                        </x-form-input>

                        {{-- Percentage slider --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-sm font-medium text-slate-700">
                                    Persentase Progres <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-1.5 rounded-lg bg-primary-50 px-2.5 py-1">
                                    <input type="number" name="progress_percentage" x-model="pct"
                                           min="0" max="100"
                                           class="w-12 bg-transparent text-center text-sm font-bold text-primary-700 focus:outline-none">
                                    <span class="text-sm font-bold text-primary-600">%</span>
                                </div>
                            </div>
                            <input type="range" x-model="pct" min="0" max="100" step="5"
                                   class="w-full accent-primary-600 cursor-pointer">
                            <div class="mt-2">
                                <x-progress-bar :percentage="0" :show-label="false" size="sm" />
                            </div>
                            {{-- Real visual progress synced with Alpine --}}
                            <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-primary-500 transition-all duration-300"
                                     :style="'width:' + pct + '%'"></div>
                            </div>
                        </div>

                        <x-form-input name="description" label="Deskripsi" :required="true">
                            <textarea name="description" rows="3" maxlength="1000"
                                      @input="descLen = $event.target.value.length"
                                      placeholder="Jelaskan progres perbaikan yang telah dilakukan..."
                                      class="w-full resize-none rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                            <div class="mt-1 flex justify-end">
                                <span class="text-xs text-slate-400" x-text="descLen + '/1000'"></span>
                            </div>
                        </x-form-input>

                        <x-form-input name="work_date" label="Tanggal Pengerjaan">
                            <input type="date" name="work_date" value="{{ old('work_date', today()->format('Y-m-d')) }}"
                                   class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100">
                        </x-form-input>

                        <x-form-input name="image" label="Foto Dokumentasi (opsional)">
                            <input type="file" name="image" accept="image/*"
                                   class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-primary-700 hover:file:bg-primary-100 focus:outline-none">
                        </x-form-input>
                    </div>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700 transition-colors">
                        @include('layouts.icons', ['icon' => 'chart-bar', 'class' => 'h-4 w-4'])
                        Simpan Progres
                    </button>
                </form>
            </x-card>
        </div>

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
