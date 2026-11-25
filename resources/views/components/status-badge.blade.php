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
        'menunggu'        => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200/70',
        'ditinjau'        => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200/70',
        'diproses'        => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200/70',
        'dalam_perbaikan' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200/70',
        'selesai'         => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/70',
        'ditolak'         => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200/70',
    ];
    $dots = [
        'menunggu'        => 'bg-amber-400',
        'ditinjau'        => 'bg-sky-400',
        'diproses'        => 'bg-indigo-400',
        'dalam_perbaikan' => 'bg-orange-400',
        'selesai'         => 'bg-emerald-500',
        'ditolak'         => 'bg-rose-400',
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
