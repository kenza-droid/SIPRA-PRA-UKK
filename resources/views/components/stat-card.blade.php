@props([
    'label',
    'value',
    'icon'      => 'chart-bar',
    'color'     => 'indigo',
    'subtext'   => null,
    'trend'     => null,
])
@php
    $colors = [
        'indigo'  => [
            'bg'   => 'bg-indigo-50/80 text-indigo-600 ring-1 ring-indigo-100/80',
            'val'  => 'text-slate-900',
        ],
        'purple'  => [
            'bg'   => 'bg-purple-50/80 text-purple-600 ring-1 ring-purple-100/80',
            'val'  => 'text-slate-900',
        ],
        'amber'   => [
            'bg'   => 'bg-amber-50/80 text-amber-600 ring-1 ring-amber-100/80',
            'val'  => 'text-slate-900',
        ],
        'blue'    => [
            'bg'   => 'bg-sky-50/80 text-sky-600 ring-1 ring-sky-100/80',
            'val'  => 'text-slate-900',
        ],
        'orange'  => [
            'bg'   => 'bg-orange-50/80 text-orange-600 ring-1 ring-orange-100/80',
            'val'  => 'text-slate-900',
        ],
        'emerald' => [
            'bg'   => 'bg-emerald-50/80 text-emerald-600 ring-1 ring-emerald-100/80',
            'val'  => 'text-slate-900',
        ],
        'green'   => [
            'bg'   => 'bg-emerald-50/80 text-emerald-600 ring-1 ring-emerald-100/80',
            'val'  => 'text-slate-900',
        ],
        'red'     => [
            'bg'   => 'bg-rose-50/80 text-rose-600 ring-1 ring-rose-100/80',
            'val'  => 'text-slate-900',
        ],
        'slate'   => [
            'bg'   => 'bg-slate-100/80 text-slate-600 ring-1 ring-slate-200/70',
            'val'  => 'text-slate-900',
        ],
    ];
    $c = $colors[$color] ?? $colors['indigo'];
@endphp
<div class="stat-card group flex items-center gap-3.5 rounded-2xl bg-white p-4 sm:p-5 border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300/80 h-full">
    {{-- Left: Soft Pastel Icon Container --}}
    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl {{ $c['bg'] }} transition-transform duration-200 group-hover:scale-105">
        @include('layouts.icons', ['icon' => $icon, 'class' => 'h-5 w-5'])
    </div>

    {{-- Right: Label & Value --}}
    <div class="min-w-0 flex-1">
        <p class="truncate text-xs font-medium text-slate-500">
            {{ $label }}
        </p>
        <p class="mt-0.5 text-2xl font-bold tracking-tight tabular-nums {{ $c['val'] }}">
            {{ number_format($value) }}
        </p>
        @if($subtext)
            <p class="mt-0.5 truncate text-[11px] text-slate-400">{{ $subtext }}</p>
        @endif
        @if($trend !== null)
            <div class="mt-1 flex items-center gap-1 text-[11px] font-semibold {{ $trend >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                <span>{{ $trend >= 0 ? '↑' : '↓' }}</span>
                <span>{{ abs($trend) }}% dari bulan lalu</span>
            </div>
        @endif
    </div>
</div>
