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
        'indigo'  => ['bg' => 'bg-primary-50',  'icon' => 'bg-primary-100 text-primary-600',  'val' => 'text-primary-700'],
        'amber'   => ['bg' => 'bg-amber-50',    'icon' => 'bg-amber-100 text-amber-600',      'val' => 'text-amber-700'],
        'blue'    => ['bg' => 'bg-blue-50',     'icon' => 'bg-blue-100 text-blue-600',        'val' => 'text-blue-700'],
        'purple'  => ['bg' => 'bg-purple-50',   'icon' => 'bg-purple-100 text-purple-600',    'val' => 'text-purple-700'],
        'orange'  => ['bg' => 'bg-orange-50',   'icon' => 'bg-orange-100 text-orange-600',    'val' => 'text-orange-700'],
        'emerald' => ['bg' => 'bg-emerald-50',  'icon' => 'bg-emerald-100 text-emerald-600',  'val' => 'text-emerald-700'],
        'red'     => ['bg' => 'bg-red-50',      'icon' => 'bg-red-100 text-red-600',          'val' => 'text-red-700'],
        'slate'   => ['bg' => 'bg-slate-50',    'icon' => 'bg-slate-100 text-slate-600',      'val' => 'text-slate-700'],
    ];
    $c = $colors[$color] ?? $colors['indigo'];
@endphp
<div class="stat-card rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 hover:ring-primary-100 transition-all">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold {{ $c['val'] }}">{{ number_format($value) }}</p>
            @if($subtext)
                <p class="mt-1 text-xs text-slate-400">{{ $subtext }}</p>
            @endif
        </div>
        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl {{ $c['icon'] }}">
            @include('layouts.icons', ['icon' => $icon, 'class' => 'h-5 w-5'])
        </div>
    </div>
    @if($trend !== null)
        <div class="mt-3 flex items-center gap-1 text-xs {{ $trend >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
            <span>{{ $trend >= 0 ? '↑' : '↓' }}</span>
            <span>{{ abs($trend) }}% dari bulan lalu</span>
        </div>
    @endif
</div>
