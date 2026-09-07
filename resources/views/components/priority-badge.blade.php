@props(['priority', 'size' => 'sm'])
@php
    $labels  = ['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi', 'kritis' => 'Kritis'];
    $classes = [
        'rendah' => 'bg-slate-100 text-slate-600',
        'sedang' => 'bg-blue-100 text-blue-600',
        'tinggi' => 'bg-orange-100 text-orange-600',
        'kritis' => 'bg-red-100 text-red-700 font-semibold',
    ];
    $icons = [
        'rendah' => '↓',
        'sedang' => '→',
        'tinggi' => '↑',
        'kritis' => '⚡',
    ];
    $sizeClass = $size === 'lg' ? 'px-3 py-1.5 text-sm' : 'px-2.5 py-1 text-xs';
    $label = $labels[$priority] ?? $priority;
    $cls   = $classes[$priority] ?? 'bg-gray-100 text-gray-600';
    $icon  = $icons[$priority] ?? '';
@endphp
<span class="inline-flex items-center gap-1 rounded-full font-medium {{ $cls }} {{ $sizeClass }}">
    <span>{{ $icon }}</span>{{ $label }}
</span>
