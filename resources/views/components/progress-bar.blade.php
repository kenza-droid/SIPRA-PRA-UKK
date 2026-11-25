@props(['percentage' => 0, 'showLabel' => true, 'size' => 'md'])
@php
    $pct = max(0, min(100, (int) $percentage));
    $color = match(true) {
        $pct >= 100 => 'bg-emerald-500/90',
        $pct >= 75  => 'bg-sky-500/90',
        $pct >= 50  => 'bg-indigo-500/90',
        $pct >= 25  => 'bg-amber-400/90',
        default     => 'bg-indigo-400/75',
    };
    $trackColor = 'bg-slate-100';
    $height = $size === 'lg' ? 'h-2.5' : ($size === 'sm' ? 'h-1.5' : 'h-2');
@endphp
<div class="w-full">
    @if($showLabel)
        <div class="mb-1.5 flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500">Progres</span>
            <span class="text-xs font-semibold text-slate-700 tabular-nums">{{ $pct }}%</span>
        </div>
    @endif
    <div class="w-full overflow-hidden rounded-full {{ $trackColor }} {{ $height }}">
        <div class="h-full rounded-full transition-all duration-700 ease-out {{ $color }}"
             style="width: {{ $pct }}%">
        </div>
    </div>
</div>
