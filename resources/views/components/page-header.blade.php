@props(['title', 'subtitle' => null, 'backRoute' => null, 'backLabel' => 'Kembali'])
<div class="mb-6 flex items-start gap-4">
    @if($backRoute)
        <a href="{{ route($backRoute) }}"
           class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
    @endif
    <div>
        <h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>
        @if($subtitle)
            <p class="mt-0.5 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="ml-auto flex items-center gap-2">{{ $slot }}</div>
</div>
