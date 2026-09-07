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
        <li class="relative flex gap-4 pb-6 {{ $isLast ? 'pb-0' : '' }}">
            {{-- Line --}}
            @if(!$isLast)
                <span class="absolute left-[18px] top-9 bottom-0 w-0.5 {{ $isDone ? 'bg-primary-200' : 'bg-slate-100' }}"></span>
            @endif

            {{-- Dot --}}
            <div class="flex-shrink-0">
                @if($isDone)
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 shadow-sm shadow-primary-200 ring-4 ring-primary-50">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                @elseif($isActive)
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 ring-4 ring-primary-100 shadow-md shadow-primary-200">
                        <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-white"></span>
                    </div>
                @else
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white ring-2 ring-slate-200">
                        <span class="h-2.5 w-2.5 rounded-full bg-slate-200"></span>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 pt-1.5">
                <p class="text-sm font-semibold {{ $isPending ? 'text-slate-400' : 'text-slate-800' }}">
                    {{ $step['label'] }}
                </p>
                @if(isset($step['time']) && $step['time'])
                    <p class="text-xs text-slate-400 mt-0.5">{{ $step['time'] }}</p>
                @endif
                @if(isset($step['note']) && $step['note'])
                    <p class="mt-1 text-xs text-slate-500 bg-slate-50 rounded-lg px-3 py-2">{{ $step['note'] }}</p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
