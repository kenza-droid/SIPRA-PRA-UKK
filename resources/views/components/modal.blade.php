@props(['id', 'title', 'size' => 'md'])
@php
    $sizes = ['sm' => 'max-w-md', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp
<div id="{{ $id }}"
     x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
     x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click="open = false">
    </div>

    {{-- Modal panel --}}
    <div class="relative w-full {{ $sizeClass }} rounded-2xl bg-white shadow-2xl"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
            <button @click="open = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5">{{ $slot }}</div>

        @isset($footer)
            <div class="border-t border-slate-100 px-6 py-4">{{ $footer }}</div>
        @endisset
    </div>
</div>
