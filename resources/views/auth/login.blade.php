<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — SIPRA</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21.25l2.255-3.633a1.15 1.15 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z'/></svg>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
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
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }

        .left-panel-bg {
            background: linear-gradient(145deg, #1e1b4b 0%, #312e81 45%, #3730a3 100%);
        }

        .input-field {
            width: 100%;
            height: 48px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding-left: 2.75rem;
            padding-right: 1rem;
            font-size: 0.9375rem;
            color: #0f172a;
            background-color: #f8fafc;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            outline: none;
        }
        .input-field::placeholder {
            color: #94a3b8;
        }
        .input-field:focus {
            border-color: #4f46e5;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }
        .input-field.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        .input-field.error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }

        .role-tab {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            height: 38px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
        }
        .role-tab:hover {
            color: #334155;
        }
        .role-tab.active {
            background-color: #ffffff;
            color: #0f172a;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            height: 48px;
            border-radius: 10px;
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9375rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
        .btn-primary:active {
            background-color: #3730a3;
            transform: scale(0.995);
        }
        .btn-primary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }
        .btn-primary:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-900" x-data="loginPage()">

<div class="flex min-h-screen">

    {{-- ══════════════════════════════════════════════════════════
         LEFT PANEL — Branding & Product Preview
    ══════════════════════════════════════════════════════════ --}}
    <div class="relative hidden w-1/2 left-panel-bg flex-col justify-between p-12 lg:flex xl:p-16">
        
        {{-- Subtle Grid Pattern Background --}}
        <div class="absolute inset-0 opacity-5 pointer-events-none"
             style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>

        {{-- Top Branding Header --}}
        <div class="relative z-10 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 border border-white/15 shadow-sm backdrop-blur-sm">
                <svg class="h-5 w-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 9h3.75M19.5 7.5c0-.414-.168-.79-.44-1.06l-4.5-4.5A1.5 1.5 0 0013.5 1.5H6.75A2.25 2.25 0 004.5 3.75v16.5A2.25 2.25 0 006.75 22.5h10.5a2.25 2.25 0 002.25-2.25V7.5z" />
                </svg>
            </div>
            <div>
                <h1 class="text-base font-bold tracking-tight text-white leading-none">SIPRA</h1>
                <p class="text-xs text-indigo-200/80 mt-1">Sistem Informasi Pengaduan Sarana dan Prasarana</p>
            </div>
        </div>

        {{-- Middle Hero Copy + Product Preview Card --}}
        <div class="relative z-10 my-auto py-12 max-w-lg">
            {{-- Copy Header --}}
            <div class="mb-8">
                <h2 class="text-3xl font-bold tracking-tight text-white leading-tight">
                    Satu tempat untuk setiap pengaduan.
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-indigo-200/80">
                    Sampaikan masalah sarana sekolah dan pantau proses penyelesaiannya secara transparan.
                </p>
            </div>

            {{-- Single Crisp Product Preview Card --}}
            <div class="rounded-xl border border-white/10 bg-white/5 p-5 shadow-2xl backdrop-blur-sm">
                <div class="mb-4 flex items-center justify-between border-b border-white/10 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-indigo-200">Pengaduan Terbaru</span>
                    </div>
                    <span class="text-[11px] font-medium text-indigo-300/80">SIPRA Real-time</span>
                </div>

                <div class="space-y-2.5">
                    {{-- Item 1: Diproses --}}
                    <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.04] p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-indigo-500/20 text-indigo-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 2.625a3.375 3.375 0 00-3.75-3.375m-3.75 3.375a3.375 3.375 0 013.75-3.375" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white">Lampu kelas rusak</p>
                                <p class="text-[11px] text-indigo-200/70">Ruang 8</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-indigo-500/20 px-2.5 py-1 text-[11px] font-semibold text-indigo-200 border border-indigo-500/30">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                            Diproses
                        </span>
                    </div>

                    {{-- Item 2: Selesai --}}
                    <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.04] p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-500/20 text-emerald-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white">Kursi rusak</p>
                                <p class="text-[11px] text-indigo-200/70">Ruang 12</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-500/20 px-2.5 py-1 text-[11px] font-semibold text-emerald-300 border border-emerald-500/30">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Selesai
                        </span>
                    </div>

                    {{-- Item 3: Menunggu --}}
                    <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.04] p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-amber-500/20 text-amber-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white">Keran bocor</p>
                                <p class="text-[11px] text-indigo-200/70">Ruang 5</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-500/20 px-2.5 py-1 text-[11px] font-semibold text-amber-300 border border-amber-500/30">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                            Menunggu
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Footer Note --}}
        <div class="relative z-10 flex items-center justify-between text-xs text-indigo-200/60 border-t border-white/10 pt-4">
            <span>&copy; {{ date('Y') }} SIPRA</span>
            <span>Platform Pengaduan Sekolah</span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         RIGHT PANEL — Login Form
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-1 flex-col items-center justify-center bg-white px-6 py-12 sm:px-10">

        {{-- Mobile Header Logo --}}
        <div class="mb-8 flex items-center justify-center gap-3 lg:hidden">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-sm">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 9h3.75M19.5 7.5c0-.414-.168-.79-.44-1.06l-4.5-4.5A1.5 1.5 0 0013.5 1.5H6.75A2.25 2.25 0 004.5 3.75v16.5A2.25 2.25 0 006.75 22.5h10.5a2.25 2.25 0 002.25-2.25V7.5z" />
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-slate-900 leading-none">SIPRA</p>
                <p class="text-xs text-slate-500 mt-1">Sarana &amp; Prasarana Sekolah</p>
            </div>
        </div>

        <div class="w-full max-w-[400px]">

            {{-- Title & Subtitle Header --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Selamat Datang</h2>
                <p class="mt-1 text-sm text-slate-500">Masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            {{-- Flash Session Error --}}
            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
                    <svg class="h-5 w-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Role Switcher Segmented Tab --}}
            <div class="mb-6 rounded-lg bg-slate-100 p-1 border border-slate-200/60 flex gap-1">
                <button type="button"
                        @click="role = 'siswa'"
                        :class="role === 'siswa' ? 'active' : ''"
                        class="role-tab">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                    <span>Siswa</span>
                </button>
                <button type="button"
                        @click="role = 'admin'"
                        :class="role === 'admin' ? 'active' : ''"
                        class="role-tab">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <span>Admin</span>
                </button>
            </div>

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login.post') }}" @submit="submitting = true" class="space-y-5">
                @csrf
                <input type="hidden" name="role" :value="role">

                {{-- NISN / Email / Username Field --}}
                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        <span x-text="role === 'siswa' ? 'NISN / Email' : 'Email / Username'"></span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="login"
                               id="login"
                               x-model="login"
                               :placeholder="role === 'siswa' ? 'Masukkan NISN atau email' : 'Masukkan email atau username'"
                               class="input-field {{ $errors->has('login') ? 'error' : '' }}"
                               required
                               autocomplete="username">
                    </div>
                    @error('login')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div x-data="{ showPass: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                        <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                            Lupa kata sandi?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </div>
                        <input :type="showPass ? 'text' : 'password'"
                               name="password"
                               id="password"
                               x-model="password"
                               placeholder="Masukkan kata sandi"
                               class="input-field pr-10 {{ $errors->has('password') ? 'error' : '' }}"
                               required
                               autocomplete="current-password">
                        <button type="button"
                                @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                            <template x-if="!showPass">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </template>
                            <template x-if="showPass">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                {{-- Remember Me Checkbox --}}
                <div class="flex items-center gap-2.5">
                    <input type="checkbox"
                           name="remember"
                           id="remember"
                           class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-xs text-slate-600 cursor-pointer select-none">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                {{-- Primary Submit Button --}}
                <button type="submit" class="btn-primary" :disabled="submitting">
                    <template x-if="submitting">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </template>
                    <template x-if="!submitting">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                    </template>
                    <span x-text="submitting ? 'Memproses...' : 'Masuk ke SIPRA'"></span>
                </button>
            </form>

            {{-- Subtle Demo Account Box --}}
            <div class="mt-8 rounded-xl border border-slate-200/80 bg-slate-50/80 p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Akun Demo</span>
                    <span class="text-[11px] text-slate-400">Klik untuk isi otomatis</span>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between rounded-lg border border-slate-200/60 bg-white p-2.5 transition-colors hover:border-indigo-200">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 border border-indigo-100">Siswa</span>
                            <span class="text-xs font-medium text-slate-700">0051234567</span>
                            <span class="text-[11px] text-slate-400 font-mono">(NISN)</span>
                        </div>
                        <button type="button" 
                                @click="useDemo('siswa', '0051234567', 'siswa123')"
                                class="rounded-md px-2 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors">
                            Gunakan
                        </button>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200/60 bg-white p-2.5 transition-colors hover:border-indigo-200">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700 border border-slate-200">Admin</span>
                            <span class="text-xs font-medium text-slate-700">admin@sipra.sch.id</span>
                        </div>
                        <button type="button" 
                                @click="useDemo('admin', 'admin@sipra.sch.id', 'admin123')"
                                class="rounded-md px-2 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors">
                            Gunakan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <p class="mt-6 text-center text-xs text-slate-400 leading-relaxed">
                Dengan masuk, Anda menyetujui ketentuan penggunaan platform SIPRA.
            </p>
        </div>
    </div>
</div>

<script>
function loginPage() {
    return {
        role: '{{ old("role", "siswa") }}',
        login: '{{ old("login", "") }}',
        password: '',
        submitting: false,
        useDemo(roleName, loginVal, passVal) {
            this.role = roleName;
            this.login = loginVal;
            this.password = passVal;
        }
    }
}
</script>
</body>
</html>
