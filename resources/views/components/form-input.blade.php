@props(['label', 'name', 'type' => 'text', 'required' => false, 'hint' => null])
<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-1.5">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    {{ $slot }}
    @error($name)
        <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ $message }}
        </p>
    @enderror
    @if($hint && !$errors->has($name))
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
