@props(['action', 'method' => 'GET'])
<form method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}"
      x-data="{ submitting: false }"
      @submit="submitting = true"
      class="rounded-2xl bg-white p-4 border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
    @if(strtoupper($method) !== 'GET') @csrf @endif
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 min-w-0">
        {{ $slot }}
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="submit" :disabled="submitting"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 active:scale-[0.98] disabled:opacity-70 transition-all duration-150 shadow-xs motion-reduce:transition-none motion-reduce:transform-none">
            <svg x-show="!submitting" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <span x-text="submitting ? 'Memuat...' : 'Filter'">Filter</span>
        </button>
        <a href="{{ $action }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-all duration-150 motion-reduce:transition-none motion-reduce:transform-none">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
            Reset
        </a>
        {{ $actions ?? '' }}
    </div>
</form>
