@props(['status', 'size' => 'sm'])
@php
    $labels = [
        'menunggu'        => 'Menunggu',
        'ditinjau'        => 'Ditinjau',
        'diproses'        => 'Diproses',
        'dalam_perbaikan' => 'Dalam Perbaikan',
        'selesai'         => 'Selesai',
        'ditolak'         => 'Ditolak',
    ];
    $classes = [
        'menunggu'        => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
        'ditinjau'        => 'bg-blue-100 text-blue-700 ring-1 ring-blue-200',
        'diproses'        => 'bg-purple-100 text-purple-700 ring-1 ring-purple-200',
        'dalam_perbaikan' => 'bg-orange-100 text-orange-700 ring-1 ring-orange-200',
        'selesai'         => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
        'ditolak'         => 'bg-red-100 text-red-700 ring-1 ring-red-200',
    ];
    $dots = [
        'menunggu'        => 'bg-amber-400',
        'ditinjau'        => 'bg-blue-400',
        'diproses'        => 'bg-purple-400',
        'dalam_perbaikan' => 'bg-orange-400',
        'selesai'         => 'bg-emerald-400',
        'ditolak'         => 'bg-red-400',
    ];
    $sizeClass = $size === 'lg' ? 'px-3 py-1.5 text-sm' : ($size === 'xs' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs');
    $label = $labels[$status] ?? $status;
    $cls = $classes[$status] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200';
    $dot = $dots[$status] ?? 'bg-gray-400';
@endphp
<span class="inline-flex items-center gap-1.5 rounded-full font-medium {{ $cls }} {{ $sizeClass }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ $label }}
</span>
