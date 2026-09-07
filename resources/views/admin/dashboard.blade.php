@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan dan statistik aspirasi masuk')

@section('content')

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
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-primary-200 hover:bg-primary-700 transition-colors">
        @include('layouts.icons', ['icon' => 'inbox-stack', 'class' => 'h-4 w-4'])
        Kelola Aspirasi
    </a>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────── --}}
<div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
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
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Aspirasi per Bulan</h3>
                <p class="text-xs text-slate-400">6 bulan terakhir</p>
            </div>
        </div>
        <div class="relative h-56">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    {{-- Status donut --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Distribusi Status</h3>
            <p class="text-xs text-slate-400">Semua waktu</p>
        </div>
        <div class="relative mx-auto h-44 w-44">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="mt-4 space-y-1.5">
            @foreach(\App\Models\Complaint::STATUSES as $key => $cfg)
                @php $count = $statusData[$key] ?? 0; @endphp
                @if($count > 0)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 flex-shrink-0 rounded-full
                                {{ match($key) {
                                    'menunggu'        => 'bg-amber-400',
                                    'ditinjau'        => 'bg-blue-400',
                                    'diproses'        => 'bg-purple-400',
                                    'dalam_perbaikan' => 'bg-orange-400',
                                    'selesai'         => 'bg-emerald-400',
                                    'ditolak'         => 'bg-red-400',
                                    default           => 'bg-slate-400',
                                } }}">
                            </span>
                            <span class="text-xs text-slate-600">{{ $cfg['label'] }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-700">{{ $count }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- ── Second row: Category + Activity ────────────────────── --}}
<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

    {{-- Category bar chart --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Aspirasi per Kategori</h3>
            <p class="text-xs text-slate-400">Total per kategori</p>
        </div>
        <div class="relative h-52">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Aktivitas Terbaru</h3>
                <p class="text-xs text-slate-400">10 perubahan terakhir</p>
            </div>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentActivity as $activity)
                <div class="flex items-start gap-3 px-5 py-3">
                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full
                        {{ match($activity->action_color) {
                            'indigo'  => 'bg-indigo-100 text-indigo-600',
                            'blue'    => 'bg-blue-100 text-blue-600',
                            'purple'  => 'bg-purple-100 text-purple-600',
                            'emerald' => 'bg-emerald-100 text-emerald-600',
                            'amber'   => 'bg-amber-100 text-amber-600',
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
                        <p class="text-[10px] text-slate-300 mt-0.5">
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
<div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div class="flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-100 text-red-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Aspirasi yang Membutuhkan Perhatian</h3>
                <p class="text-xs text-slate-400">Belum ditinjau, prioritas tinggi, atau sudah lama</p>
            </div>
        </div>
        <a href="{{ route('admin.complaints.index', ['status' => 'menunggu']) }}"
           class="text-xs font-semibold text-primary-600 hover:text-primary-700">Lihat semua →</a>
    </div>

    @if($needAttention->isEmpty())
        <div class="px-5 py-10 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 mb-3">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-600">Semua aspirasi sudah ditangani</p>
            <p class="mt-1 text-xs text-slate-400">Tidak ada aspirasi yang membutuhkan perhatian segera.</p>
        </div>
    @else
        <div class="divide-y divide-slate-50">
            @foreach($needAttention as $c)
                <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/60 transition-colors">
                    {{-- Priority indicator --}}
                    <div class="flex-shrink-0 w-1.5 self-stretch rounded-full
                        {{ match($c->priority) { 'kritis' => 'bg-red-500', 'tinggi' => 'bg-orange-400', 'sedang' => 'bg-blue-400', default => 'bg-slate-200' } }}">
                    </div>

                    {{-- Avatar --}}
                    <img src="{{ $c->user->avatar_url }}" alt="{{ $c->user->name }}"
                         class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-100">

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800">{{ $c->title }}</p>
                        <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                            <span class="text-xs text-slate-400">{{ $c->user->name }}</span>
                            <span class="text-xs text-slate-300">·</span>
                            <span class="text-xs text-slate-400">{{ $c->category->name }}</span>
                            <span class="text-xs text-slate-300">·</span>
                            <span class="text-xs text-slate-400">{{ $c->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-shrink-0 items-center gap-2">
                        <x-priority-badge :priority="$c->priority" />
                        <x-status-badge :status="$c->status" />
                    </div>

                    {{-- CTA --}}
                    <a href="{{ route('admin.complaints.show', $c) }}"
                       class="flex-shrink-0 rounded-lg border border-slate-200 bg-white p-2 text-slate-400 shadow-sm hover:border-primary-200 hover:text-primary-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

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
        menunggu: '#fbbf24', ditinjau: '#60a5fa', diproses: '#a78bfa',
        dalam_perbaikan: '#fb923c', selesai: '#34d399', ditolak: '#f87171',
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
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
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
                    '#818cf8','#34d399','#fbbf24','#f87171','#60a5fa',
                    '#a78bfa','#fb923c','#2dd4bf','#f472b6','#94a3b8',
                ],
                borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 }, beginAtZero: true },
                y: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
            }
        }
    });

});
</script>
@endpush
