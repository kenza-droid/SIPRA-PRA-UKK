@props(['steps'])
{{--
$steps = array of:
  ['key', 'label', 'icon', 'state' => 'done|active|pending']
--}}
<ol class="relative space-y-0">
    @foreach($steps as $i => $step)
        @php
            $isLast = $i === count($steps) - 1;
            $isDone  = $step['state'] === 'done';
            $isActive = $step['state'] === 'active';
            $isPending = $step['state'] === 'pending';
        @endphp
        <li class="relative flex gap-3.5 pb-5 {{ $isLast ? 'pb-0' : '' }}">
            {{-- Line --}}
            @if(!$isLast)
                <span class="absolute left-[15px] top-8 bottom-0 w-px {{ $isDone ? 'bg-indigo-200' : 'bg-slate-200/80' }}"></span>
            @endif

            {{-- Dot --}}
            <div class="flex-shrink-0">
                @if($isDone)
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                @elseif($isActive)
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 ring-4 ring-indigo-100">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-white"></span>
                    </div>
                @else
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 ring-1 ring-slate-200">
                        <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 pt-1">
                <p class="text-sm {{ $isPending ? 'font-medium text-slate-400' : 'font-semibold text-slate-800' }}">
                    {{ $step['label'] }}
                </p>
                @if(isset($step['time']) && $step['time'])
                    <p class="text-xs text-slate-400 mt-0.5">{{ $step['time'] }}</p>
                @endif
                @if(isset($step['note']) && $step['note'])
                    <p class="mt-1.5 text-xs text-slate-600 bg-slate-50/90 border border-slate-100 rounded-xl px-3 py-2">{{ $step['note'] }}</p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
