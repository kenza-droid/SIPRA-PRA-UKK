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
            'bg'   => 'bg-indigo-50 text-indigo-600',
            'val'  => 'text-indigo-950',
        ],
        'purple'  => [
            'bg'   => 'bg-purple-50 text-purple-600',
            'val'  => 'text-purple-900',
        ],
        'amber'   => [
            'bg'   => 'bg-amber-50 text-amber-600',
            'val'  => 'text-amber-700',
        ],
        'blue'    => [
            'bg'   => 'bg-blue-50 text-blue-600',
            'val'  => 'text-blue-600',
        ],
        'orange'  => [
            'bg'   => 'bg-orange-50 text-orange-600',
            'val'  => 'text-orange-700',
        ],
        'emerald' => [
            'bg'   => 'bg-emerald-50 text-emerald-600',
            'val'  => 'text-emerald-700',
        ],
        'green'   => [
            'bg'   => 'bg-emerald-50 text-emerald-600',
            'val'  => 'text-emerald-700',
        ],
        'red'     => [
            'bg'   => 'bg-red-50 text-red-600',
            'val'  => 'text-red-700',
        ],
        'slate'   => [
            'bg'   => 'bg-slate-100 text-slate-600',
            'val'  => 'text-slate-900',
        ],
    ];
    $c = $colors[$color] ?? $colors['indigo'];
@endphp
<div class="stat-card group flex items-center gap-3.5 sm:gap-4 rounded-2xl bg-white p-4 sm:p-5 shadow-xs border border-slate-100/90 hover:border-slate-200 hover:shadow-md transition-all duration-200 min-h-[100px] sm:min-h-[110px] h-full">
    {{-- Left: Pastel Icon Container --}}
    <div class="flex h-12 w-12 sm:h-[52px] sm:w-[52px] flex-shrink-0 items-center justify-center rounded-2xl {{ $c['bg'] }} transition-transform duration-200 group-hover:scale-105">
        @include('layouts.icons', ['icon' => $icon, 'class' => 'h-6 w-6'])
    </div>

    {{-- Right: Label & Value --}}
    <div class="min-w-0 flex-1">
        <p class="truncate text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500">
            {{ $label }}
        </p>
        <p class="mt-0.5 text-2xl sm:text-3xl font-extrabold tracking-tight {{ $c['val'] }}">
            {{ number_format($value) }}
        </p>
        @if($subtext)
            <p class="mt-0.5 truncate text-[11px] text-slate-400">{{ $subtext }}</p>
        @endif
        @if($trend !== null)
            <div class="mt-1 flex items-center gap-1 text-[11px] font-semibold {{ $trend >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                <span>{{ $trend >= 0 ? '↑' : '↓' }}</span>
                <span>{{ abs($trend) }}% dari bulan lalu</span>
            </div>
        @endif
    </div>
</div>
