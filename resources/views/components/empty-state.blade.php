@props(['title' => 'Tidak ada data', 'description' => null, 'action' => null, 'actionLabel' => null, 'actionRoute' => null, 'icon' => 'inbox'])
<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200/90 bg-slate-50/50 px-6 py-12 text-center">
    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-slate-200/70 text-slate-400 mb-3 shadow-xs">
        @include('layouts.icons', ['icon' => $icon, 'class' => 'h-6 w-6'])
    </div>
    <h3 class="text-sm font-semibold text-slate-700">{{ $title }}</h3>
    @if($description)
        <p class="mt-1.5 max-w-sm text-xs text-slate-500 leading-relaxed">{{ $description }}</p>
    @endif
    @if($actionRoute && $actionLabel)
        <a href="{{ route($actionRoute) }}"
           class="mt-5 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-primary-700 transition-colors">
            {{ $actionLabel }}
        </a>
    @endif
    {{ $slot }}
</div>
