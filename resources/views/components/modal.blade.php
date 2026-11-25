@props(['id', 'title', 'size' => 'md'])
@php
    $sizes = ['sm' => 'max-w-md', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp
<div id="{{ $id }}"
     x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
     x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     role="dialog"
     aria-modal="true"
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Backdrop --}}
    <div x-show="open"
         class="absolute inset-0 bg-slate-900/40 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
    </div>

    {{-- Modal panel --}}
    <div x-show="open"
         class="relative flex flex-col max-h-[90vh] w-full {{ $sizeClass }} rounded-2xl bg-white border border-slate-200/80 shadow-xl"
         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none motion-reduce:transform-none"
         x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none motion-reduce:transform-none"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]">

        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
            <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
            <button type="button" @click="open = false" aria-label="Tutup modal" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto px-5 py-5 sm:px-6">{{ $slot }}</div>

        @isset($footer)
            <div class="shrink-0 border-t border-slate-100 px-5 py-4 sm:px-6">{{ $footer }}</div>
        @endisset
    </div>
</div>
