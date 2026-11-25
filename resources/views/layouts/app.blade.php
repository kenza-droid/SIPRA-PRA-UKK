<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SIPRA</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234f46e5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21.25l2.255-3.633a1.15 1.15 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z'/></svg>">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.25s ease-out',
                        'slide-in': 'slideIn 0.25s ease-out',
                        'bounce-in': 'bounceIn 0.35s ease-out',
                    },
                    keyframes: {
                        fadeIn:   { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideIn:  { '0%': { opacity: '0', transform: 'translateY(-6px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        bounceIn: { '0%': { transform: 'scale(0.95)', opacity: '0' }, '100%': { transform: 'scale(1)', opacity: '1' } },
                    },
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .sidebar-link {
            position: relative;
            transition: all 150ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link.active {
            background: #eef2ff;
            color: #4338ca;
            font-weight: 600;
            box-shadow: inset 0 0 0 1px rgba(99, 102, 241, 0.16);
        }
        .sidebar-link:not(.active):hover {
            background: #f8fafc;
            color: #0f172a;
        }
        .sidebar-link:active {
            transform: scale(0.99);
        }
        .stat-card { transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
        .stat-card:hover { transform: translateY(-1px); box-shadow: 0 6px 20px -4px rgba(15, 23, 42, 0.06); }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .skeleton-shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s ease-in-out infinite;
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            .sidebar-link,
            .sidebar-link:active,
            .sidebar-link svg {
                transform: none !important;
                transition: none !important;
            }
            .stat-card:hover { transform: none; }
            .skeleton-shimmer { animation: none; background: #f1f5f9; }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-[#f8fafc] text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen"
     x-cloak
     @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-slate-900/30 backdrop-blur-xs lg:hidden"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

<div class="flex min-h-screen">

    {{-- ── SIDEBAR ─────────────────────────────────────── --}}
    @include('layouts.sidebar')

    {{-- ── MAIN CONTENT ────────────────────────────────── --}}
    <div class="flex flex-1 flex-col min-w-0 lg:pl-64">

        {{-- Top navbar --}}
        @include('layouts.navbar')

        {{-- Page content --}}
        <main class="flex-1 px-4 py-5 sm:px-6 sm:py-6 animate-fade-in overflow-x-clip">

            {{-- Flash messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     x-init="setTimeout(() => show = false, 5000)"
                     class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 px-4 py-3 text-emerald-800 shadow-xs animate-slide-in">
                    <svg class="h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                    <button @click="show=false" aria-label="Tutup notifikasi" class="ml-auto text-emerald-600/70 hover:text-emerald-800"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     x-init="setTimeout(() => show = false, 6000)"
                     class="mb-6 flex items-center gap-3 rounded-2xl bg-red-50/90 border border-red-200/80 px-4 py-3 text-red-800 shadow-xs animate-slide-in">
                    <svg class="h-5 w-5 flex-shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                    <button @click="show=false" aria-label="Tutup notifikasi" class="ml-auto text-red-600/70 hover:text-red-800"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        @include('layouts.footer')
    </div>
</div>

@stack('scripts')
</body>
</html>
