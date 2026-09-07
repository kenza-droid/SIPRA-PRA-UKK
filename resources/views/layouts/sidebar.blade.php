@php
    $isAdmin = auth()->user()->isAdmin();
    $currentRoute = Route::currentRouteName();

    $adminNav = [
        ['route' => 'admin.dashboard',         'label' => 'Dashboard',          'icon' => 'squares-2x2'],
        ['route' => 'admin.complaints.index',   'label' => 'Semua Aspirasi',     'icon' => 'inbox-stack'],
        ['route' => 'admin.categories.index',   'label' => 'Kategori',           'icon' => 'tag'],
        ['route' => 'admin.report',             'label' => 'Laporan',            'icon' => 'chart-bar-square'],
        ['route' => 'admin.siswa',              'label' => 'Data Siswa',         'icon' => 'users'],
        ['route' => 'admin.notifications.index','label' => 'Notifikasi',         'icon' => 'bell'],
    ];

    $siswaNav = [
        ['route' => 'siswa.dashboard',              'label' => 'Dashboard',       'icon' => 'squares-2x2'],
        ['route' => 'siswa.complaints.create',       'label' => 'Buat Aspirasi',  'icon' => 'plus-circle'],
        ['route' => 'siswa.complaints.index',        'label' => 'Riwayat',        'icon' => 'clock'],
        ['route' => 'siswa.notifications.index',     'label' => 'Notifikasi',     'icon' => 'bell'],
        ['route' => 'siswa.profile.show',            'label' => 'Profil Saya',    'icon' => 'user-circle'],
    ];

    $navItems = $isAdmin ? $adminNav : $siswaNav;
@endphp

{{-- Desktop Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col bg-white border-r border-slate-100 shadow-sm lg:flex">
    @include('layouts.sidebar-content', ['navItems' => $navItems, 'isAdmin' => $isAdmin])
</aside>

{{-- Mobile Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-30 w-72 flex-col bg-white border-r border-slate-100 shadow-xl lg:hidden flex"
       x-show="sidebarOpen"
       x-cloak
       x-transition:enter="transition ease-out duration-250"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full">
    @include('layouts.sidebar-content', ['navItems' => $navItems, 'isAdmin' => $isAdmin])
</aside>
