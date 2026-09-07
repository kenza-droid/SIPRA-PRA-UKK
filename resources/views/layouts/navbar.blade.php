<header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-100 bg-white/95 backdrop-blur px-4 sm:px-6 shadow-sm">

    {{-- Mobile hamburger --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden transition-colors">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
    </button>

    {{-- Page title --}}
    <div class="flex-1 min-w-0">
        <h1 class="truncate text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
        @hasSection('page-subtitle')
            <p class="text-xs text-slate-400 truncate">@yield('page-subtitle')</p>
        @endif
    </div>

    {{-- Right side --}}
    <div class="flex items-center gap-2">

        {{-- Notification Bell --}}
        <div class="relative" x-data="notifDropdown()" @click.outside="open = false">
            <button @click="open = !open; if(open) loadNotifs()"
                    class="relative flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
                <span x-show="unreadCount > 0" x-cloak
                      x-text="unreadCount > 99 ? '99+' : unreadCount"
                      class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-white">
                </span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                 class="absolute right-0 mt-2 w-80 origin-top-right rounded-2xl bg-white shadow-xl ring-1 ring-black/5 focus:outline-none">

                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-semibold text-slate-800">Notifikasi</p>
                    <template x-if="unreadCount > 0">
                        <form method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.notifications.read-all') : route('siswa.notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-primary-600 hover:text-primary-700">Tandai semua dibaca</button>
                        </form>
                    </template>
                </div>

                <div class="max-h-80 overflow-y-auto scrollbar-thin">
                    <template x-if="loading">
                        <div class="flex items-center justify-center py-8">
                            <svg class="h-5 w-5 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </div>
                    </template>
                    <template x-if="!loading && notifications.length === 0">
                        <div class="py-8 text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                            <p class="mt-2 text-sm text-slate-400">Tidak ada notifikasi</p>
                        </div>
                    </template>
                    <template x-for="notif in notifications" :key="notif.id">
                        <div :class="notif.is_read ? 'bg-white' : 'bg-primary-50/50'"
                             class="flex gap-3 border-b border-slate-50 px-4 py-3 hover:bg-slate-50 transition-colors cursor-pointer"
                             @click="if(notif.action_url) window.location.href = notif.action_url">
                            <div :class="notif.type === 'success' ? 'bg-emerald-100 text-emerald-600'
                                       : notif.type === 'danger' ? 'bg-red-100 text-red-600'
                                       : notif.type === 'warning' ? 'bg-amber-100 text-amber-600'
                                       : 'bg-primary-100 text-primary-600'"
                                 class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-slate-800" x-text="notif.title"></p>
                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-2" x-text="notif.message"></p>
                            </div>
                            <template x-if="!notif.is_read">
                                <span class="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-primary-500"></span>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="border-t border-slate-100 px-4 py-2">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.notifications.index') : route('siswa.notifications.index') }}"
                       class="block text-center text-xs font-medium text-primary-600 hover:text-primary-700 py-1">
                        Lihat semua notifikasi
                    </a>
                </div>
            </div>
        </div>

        {{-- User Avatar --}}
        <div class="hidden items-center gap-2 sm:flex">
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                 class="h-8 w-8 rounded-full object-cover ring-2 ring-primary-100">
            <div class="hidden md:block">
                <p class="max-w-[120px] truncate text-sm font-medium text-slate-700">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400">{{ auth()->user()->isAdmin() ? 'Administrator' : auth()->user()->class }}</p>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
function notifDropdown() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: {{ auth()->user()->unread_notifications_count }},
        async loadNotifs() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("api.notifications.dropdown") }}', {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
                const data = await res.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch(e) {}
            this.loading = false;
        }
    }
}
</script>
@endpush
