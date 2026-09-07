<div class="flex h-full flex-col overflow-y-auto scrollbar-thin">

    {{-- Brand --}}
    <div class="flex h-16 flex-shrink-0 items-center gap-3 px-5 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary-600 to-primary-500 shadow-lg shadow-primary-200">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
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

    {{-- Role badge --}}
    <div class="px-4 pt-4 pb-2">
        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold {{ $isAdmin ? 'bg-primary-50 text-primary-700' : 'bg-emerald-50 text-emerald-700' }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $isAdmin ? 'bg-primary-500' : 'bg-emerald-500' }}"></span>
            {{ $isAdmin ? 'Administrator' : 'Siswa' }}
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-0.5 px-3 py-2">
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
