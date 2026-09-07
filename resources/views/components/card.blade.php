@props(['title' => null, 'subtitle' => null, 'padding' => true, 'class' => ''])
<div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 {{ $class }}">
    @if($title)
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($action)
                <div>{{ $action }}</div>
            @endisset
        </div>
    @endif
    <div class="{{ $padding ? 'p-5' : '' }}">
        {{ $slot }}
    </div>
</div>
