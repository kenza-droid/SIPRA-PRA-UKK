<div class="flex h-full flex-col overflow-y-auto scrollbar-thin">

    {{-- Brand --}}
    <div class="flex h-16 flex-shrink-0 items-center gap-3 px-5 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 shadow-md shadow-indigo-200">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21.25l2.255-3.633a1.15 1.15 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
            </svg>
        </div>
        <div>
            <p class="text-base font-bold tracking-tight text-slate-900">SIPRA</p>
            <p class="text-[10px] font-medium text-slate-400 leading-none">Sarana &amp; Prasarana</p>
        </div>
        {{-- Close btn mobile --}}
        <button @click="sidebarOpen=false" class="ml-auto rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 lg:hidden">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-0.5 px-3 py-2 mt-4">
        @foreach($navItems as $item)
            @php
                $currentRoute = Route::currentRouteName() ?? '';

                // Exact match selalu aktif
                if ($currentRoute === $item['route']) {
                    $isActive = true;
                }
                // Riwayat (complaints.index) aktif juga saat di halaman show
                elseif ($item['route'] === 'siswa.complaints.index') {
                    $isActive = $currentRoute === 'siswa.complaints.show';
                }
                // Admin complaints index aktif saat di show
                elseif ($item['route'] === 'admin.complaints.index') {
                    $isActive = $currentRoute === 'admin.complaints.show';
                }
                // Notifikasi: aktif untuk semua sub-halaman notifikasi
                elseif (str_contains($item['route'], 'notifications.index')) {
                    $isActive = str_starts_with($currentRoute, str_replace('notifications.index', 'notifications.', $item['route']));
                }
                else {
                    $isActive = false;
                }
            @endphp
            <a href="{{ route($item['route']) }}"
               class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-all {{ $isActive ? 'active' : '' }}">
                @include('layouts.icons', ['icon' => $item['icon'], 'class' => 'h-5 w-5 flex-shrink-0'])
                {{ $item['label'] }}
                @if($item['icon'] === 'bell' && auth()->user()->unread_notifications_count > 0)
                    <span class="ml-auto flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                        {{ auth()->user()->unread_notifications_count > 99 ? '99+' : auth()->user()->unread_notifications_count }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- User info + Logout --}}
    <div class="border-t border-slate-100 p-3">
        <div class="flex items-center gap-3 rounded-xl p-2">
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                 class="h-9 w-9 rounded-full object-cover ring-2 ring-primary-100">
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-500 transition-all hover:bg-red-50 hover:text-red-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</div>
