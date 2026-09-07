@extends('layouts.app')

@section('title', 'Laporan Aspirasi')
@section('page-title', 'Laporan Aspirasi')
@section('page-subtitle', 'Analitik dan statistik pengelolaan aspirasi')

@section('content')

{{-- ── Filters ──────────────────────────────────────────────── --}}
<x-table-filter :action="route('admin.report')">
    <div class="flex gap-2 sm:col-span-2">
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
    </div>
    <select name="month" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Bulan</option>
        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $i => $m)
            <option value="{{ $i+1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
        @endforeach
    </select>
    <select name="year" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Tahun</option>
        @foreach([now()->year, now()->year-1, now()->year-2] as $y)
            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <select name="category" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Status</option>
        @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
        @endforeach
    </select>
    <select name="siswa" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        <option value="">Semua Siswa</option>
        @foreach($siswaList as $s)
            <option value="{{ $s->id }}" {{ request('siswa') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
        @endforeach
    </select>
</x-table-filter>

{{-- ── Summary KPI Cards ─────────────────────────────────────── --}}
<div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 text-center">
        <p class="text-3xl font-black text-primary-700">{{ $total }}</p>
        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Total Aspirasi</p>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 text-center">
        <p class="text-3xl font-black text-emerald-600">{{ $selesai }}</p>
        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Selesai</p>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 text-center">
        <p class="text-3xl font-black text-blue-600">{{ $completionRate }}%</p>
        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Completion Rate</p>
        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
            <div class="h-full rounded-full bg-blue-500" style="width:{{ $completionRate }}%"></div>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 text-center">
        <p class="text-3xl font-black text-amber-600">
            {{ $avgResolutionDays ? round($avgResolutionDays, 1) : '—' }}
        </p>
        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Rata-rata Hari Selesai</p>
    </div>
</div>

{{-- ── Charts Row ────────────────────────────────────────────── --}}
<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

    {{-- Per Kategori --}}
    <x-card title="Aspirasi per Kategori">
        @if($perKategori->isEmpty())
            <p class="py-6 text-center text-sm text-slate-400">Tidak ada data.</p>
        @else
            <div class="space-y-3">
                @php $maxKat = $perKategori->max(); @endphp
                @foreach($perKategori as $name => $count)
                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <p class="text-xs font-medium text-slate-600 truncate max-w-[160px]">{{ $name ?? 'Tidak diketahui' }}</p>
                            <span class="text-xs font-bold text-slate-700">{{ $count }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-primary-500 transition-all"
                                 style="width: {{ $maxKat > 0 ? round(($count / $maxKat) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    {{-- Per Status --}}
    <x-card title="Distribusi Status">
        @if($perStatus->isEmpty())
            <p class="py-6 text-center text-sm text-slate-400">Tidak ada data.</p>
        @else
            <div class="relative mx-auto mb-4 h-48 w-48">
                <canvas id="reportStatusChart"></canvas>
            </div>
            <div class="space-y-2">
                @foreach($perStatus as $status => $count)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                        <x-status-badge :status="$status" />
                        <span class="text-sm font-bold text-slate-700">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    {{-- Tren Bulanan --}}
    @if($monthlyTrend->isNotEmpty())
        <x-card title="Tren Bulanan">
            <div class="relative h-44">
                <canvas id="reportTrendChart"></canvas>
            </div>
        </x-card>
    @endif

    {{-- Per Prioritas --}}
    <x-card title="Distribusi Prioritas">
        @if($perPrioritas->isEmpty())
            <p class="py-6 text-center text-sm text-slate-400">Tidak ada data.</p>
        @else
            <div class="space-y-3">
                @php
                    $prioColors = ['rendah'=>'bg-slate-300','sedang'=>'bg-blue-400','tinggi'=>'bg-orange-400','kritis'=>'bg-red-500'];
                    $prioLabels = ['rendah'=>'Rendah','sedang'=>'Sedang','tinggi'=>'Tinggi','kritis'=>'Kritis'];
                    $maxPrio = $perPrioritas->max();
                @endphp
                @foreach($perPrioritas as $prio => $count)
                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <x-priority-badge :priority="$prio" />
                            <span class="text-xs font-bold text-slate-700">{{ $count }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full transition-all {{ $prioColors[$prio] ?? 'bg-slate-300' }}"
                                 style="width: {{ $maxPrio > 0 ? round(($count / $maxPrio) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>

{{-- ── Data Table ────────────────────────────────────────────── --}}
<div class="mt-6">
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-700">
            Daftar Aspirasi ({{ $total }} data)
        </h3>
        <div class="flex gap-2">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
                Cetak
            </button>
        </div>
    </div>

    @if($complaints->isEmpty())
        <x-empty-state title="Tidak ada data" description="Sesuaikan filter untuk melihat data." icon="chart-bar-square" />
    @else
        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <table class="w-full min-w-[700px] text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Prioritas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($complaints as $i => $c)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 max-w-[180px]">
                                <p class="truncate text-xs font-semibold text-slate-800">{{ $c->title }}</p>
                                <p class="font-mono text-[10px] text-slate-400">{{ $c->complaint_number }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ $c->user->name }}
                                <p class="text-[10px] text-slate-400">{{ $c->user->class }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500">{{ $c->category->name }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                                {{ $c->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <x-priority-badge :priority="$c->priority" />
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$c->status" />
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ $c->resolved_at?->format('d M Y') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if($perStatus->isNotEmpty())
    const statusColors = {
        menunggu:'#fbbf24',ditinjau:'#60a5fa',diproses:'#a78bfa',
        dalam_perbaikan:'#fb923c',selesai:'#34d399',ditolak:'#f87171',
    };
    const statusLabels = {
        menunggu:'Menunggu',ditinjau:'Ditinjau',diproses:'Diproses',
        dalam_perbaikan:'Dalam Perbaikan',selesai:'Selesai',ditolak:'Ditolak',
    };
    const sd = @json($perStatus);
    const sdKeys = Object.keys(sd);
    new Chart(document.getElementById('reportStatusChart'), {
        type: 'doughnut',
        data: {
            labels: sdKeys.map(k => statusLabels[k] || k),
            datasets: [{
                data: sdKeys.map(k => sd[k]),
                backgroundColor: sdKeys.map(k => statusColors[k] || '#cbd5e1'),
                borderWidth: 3, borderColor: '#ffffff', hoverOffset: 6,
            }]
        },
        options: { responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{legend:{display:false}} }
    });
    @endif

    @if($monthlyTrend->isNotEmpty())
    const trend = @json($monthlyTrend);
    new Chart(document.getElementById('reportTrendChart'), {
        type: 'line',
        data: {
            labels: Object.keys(trend),
            datasets: [{
                label: 'Aspirasi',
                data: Object.values(trend),
                borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2.5, pointBackgroundColor: '#6366f1',
                pointRadius: 4, fill: true, tension: 0.4,
            }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{
                x:{grid:{display:false},ticks:{font:{size:10},color:'#94a3b8'}},
                y:{grid:{color:'#f1f5f9'},ticks:{font:{size:10},color:'#94a3b8',stepSize:1},beginAtZero:true},
            }
        }
    });
    @endif
});
</script>
@endpush
