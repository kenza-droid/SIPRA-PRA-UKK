@props(['title' => 'Tidak ada data', 'description' => null, 'action' => null, 'actionLabel' => null, 'actionRoute' => null, 'icon' => 'inbox'])
<div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-16 text-center">
    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4">
        @include('layouts.icons', ['icon' => $icon, 'class' => 'h-8 w-8'])
    </div>
    <h3 class="text-base font-semibold text-slate-700">{{ $title }}</h3>
    @if($description)
        <p class="mt-2 max-w-sm text-sm text-slate-400">{{ $description }}</p>
    @endif
    @if($actionRoute && $actionLabel)
        <a href="{{ route($actionRoute) }}"
           class="mt-6 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
            {{ $actionLabel }}
        </a>
    @endif
    {{ $slot }}
</div>
