<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — SIPRA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: {
                            50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',
                            400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',
                            800:'#3730a3',900:'#312e81',950:'#1e1b4b',
                        },
                    },
                    keyframes: {
                        fadeUp:  { '0%': { opacity:'0', transform:'translateY(16px)' }, '100%': { opacity:'1', transform:'translateY(0)' } },
                        fadeIn:  { '0%': { opacity:'0' }, '100%': { opacity:'1' } },
                        float:   { '0%,100%': { transform:'translateY(0)' }, '50%': { transform:'translateY(-10px)' } },
                    },
                    animation: {
                        'fade-up':    'fadeUp 0.5s ease-out both',
                        'fade-in':    'fadeIn 0.4s ease-out both',
                        'float':      'float 4s ease-in-out infinite',
                        'float-slow': 'float 6s ease-in-out infinite',
                    },
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak]  { display: none !important; }
        body       { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-mesh {
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 40%, #6366f1 70%, #818cf8 100%);
        }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .input-field {
            width: 100%;
            border-radius: 0.75rem;
            border: 1.5px solid #e2e8f0;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            color: #1e293b;
            background: #f8fafc;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
        }
        .input-field:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .input-field.error {
            border-color: #ef4444;
            background: #fff7f7;
        }
        .input-field.error:focus {
            box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
        }
        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%;
            padding: 0.75rem 1.5rem;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            font-weight: 700;
            font-size: 0.9375rem;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 16px rgba(79,70,229,0.35);
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,0.4); }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .role-tab {
            flex: 1; padding: 0.5rem 0.75rem; border-radius: 0.625rem;
            font-size: 0.8125rem; font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
        }
        .role-tab.active { background: white; color: #4f46e5; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .role-tab:not(.active) { color: #64748b; }
    </style>
</head>
<body class="h-full bg-slate-50" x-data="loginPage()">

<div class="flex min-h-screen">

    {{-- ══════════════════════════════════════════════════════════
         LEFT PANEL — Branding
    ══════════════════════════════════════════════════════════ --}}
    <div class="relative hidden w-[52%] flex-col overflow-hidden gradient-mesh lg:flex">

        {{-- Decorative blobs --}}
        <div class="absolute -left-20 -top-20 h-96 w-96 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-20 h-80 w-80 rounded-full bg-primary-400/20 blur-3xl"></div>
        <div class="absolute left-1/2 top-1/3 h-64 w-64 -translate-x-1/2 rounded-full bg-indigo-400/10 blur-2xl"></div>

        {{-- Dot grid pattern --}}
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle, rgba(255,255,255,0.6) 1px, transparent 1px); background-size: 28px 28px;"></div>

        <div class="relative flex flex-1 flex-col items-center justify-center px-12 py-16 text-white">

            {{-- Logo --}}
            <div class="mb-8 flex items-center gap-4 animate-fade-up" style="animation-delay:0.1s">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 shadow-xl backdrop-blur ring-1 ring-white/30">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight">SIPRA</h1>
                    <p class="text-xs font-medium text-indigo-200">Sistem Informasi Pengaduan Sarana dan Prasarana</p>
                </div>
            </div>

            {{-- Illustration area --}}
            <div class="mb-10 animate-float" style="animation-delay:0s">
                <div class="relative mx-auto w-72">
                    {{-- Main illustration card --}}
                    <div class="glass-card rounded-2xl p-6 animate-fade-up" style="animation-delay:0.2s">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-emerald-400/20 flex items-center justify-center">
                                <svg class="h-5 w-5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">Aspirasi Terselesaikan</p>
                                <p class="text-xs text-indigo-200">Proyektor Ruang XII RPL 1</p>
                            </div>
                            <span class="ml-auto text-xs font-bold text-emerald-300 bg-emerald-400/20 rounded-full px-2 py-0.5">100%</span>
                        </div>
                        {{-- Progress bar --}}
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-white/10">
                            <div class="h-full w-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-300 transition-all duration-1000"></div>
                        </div>
                    </div>

                    {{-- Floating badge 1 --}}
                    <div class="absolute -right-6 top-0 glass-card rounded-xl px-3 py-2 animate-float-slow" style="animation-delay:1s">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                            <span class="text-xs font-medium text-white">3 Menunggu</span>
                        </div>
                    </div>

                    {{-- Floating badge 2 --}}
                    <div class="absolute -left-6 bottom-0 glass-card rounded-xl px-3 py-2 animate-float" style="animation-delay:2s">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-400 animate-pulse"></span>
                            <span class="text-xs font-medium text-white">2 Diproses</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Headline --}}
            <div class="text-center animate-fade-up" style="animation-delay:0.3s">
                <h2 class="text-3xl font-extrabold leading-tight tracking-tight">
                    Laporkan. Pantau. Perbaiki.
                </h2>
                <p class="mt-3 text-base font-medium leading-relaxed text-indigo-200 max-w-sm">
                    Satu platform untuk menyampaikan aspirasi dan memantau proses perbaikan sarana sekolah secara transparan.
                </p>
            </div>

            {{-- Feature Pills --}}
            <div class="mt-8 flex flex-wrap justify-center gap-2 animate-fade-up" style="animation-delay:0.4s">
                @foreach(['Transparan', 'Real-time', 'Mudah Digunakan', 'Terpercaya'] as $feat)
                    <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-indigo-100 ring-1 ring-white/20">
                        {{ $feat }}
                    </span>
                @endforeach
            </div>

            {{-- Stats row --}}
            <div class="mt-10 grid grid-cols-3 gap-4 animate-fade-up" style="animation-delay:0.5s">
                @foreach([['10+','Aspirasi','ditangani'],['100%','Transparan','& akuntabel'],['2','Role','pengguna']] as [$num,$lbl,$sub])
                    <div class="text-center">
                        <p class="text-2xl font-black">{{ $num }}</p>
                        <p class="text-xs font-semibold text-indigo-200">{{ $lbl }}</p>
                        <p class="text-[10px] text-indigo-300">{{ $sub }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         RIGHT PANEL — Login Form
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-1 flex-col items-center justify-center bg-white px-6 py-12 sm:px-10">

        {{-- Mobile logo --}}
        <div class="mb-8 flex items-center gap-3 lg:hidden animate-fade-up">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-600 to-primary-500 shadow-lg">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold text-slate-900">SIPRA</p>
                <p class="text-[10px] text-slate-400">Sarana &amp; Prasarana</p>
            </div>
        </div>

        <div class="w-full max-w-[400px] animate-fade-up" style="animation-delay:0.15s">

            {{-- Header --}}
            <div class="mb-8">
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Selamat Datang </h2>
                <p class="mt-1 text-sm text-slate-500">Masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            {{-- Flash error --}}
            @if(session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Role Selector --}}
            <div class="mb-6 rounded-xl bg-slate-100 p-1 flex gap-1">
                <button type="button"
                        @click="role = 'siswa'"
                        :class="role === 'siswa' ? 'active' : ''"
                        class="role-tab">
                    <div class="flex items-center justify-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                        </svg>
                        Siswa
                    </div>
                </button>
                <button type="button"
                        @click="role = 'admin'"
                        :class="role === 'admin' ? 'active' : ''"
                        class="role-tab">
                    <div class="flex items-center justify-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                        Admin
                    </div>
                </button>
            </div>

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login.post') }}" @submit="submitting = true" class="space-y-5">
                @csrf
                <input type="hidden" name="role" :value="role">

                {{-- Login field --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        <span x-text="role === 'siswa' ? 'NISN / Email' : 'Email / Username'"></span>
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="login"
                               id="login"
                               value="{{ old('login') }}"
                               :placeholder="role === 'siswa' ? 'Masukkan NISN atau email' : 'Masukkan email atau username'"
                               class="input-field pl-10 {{ $errors->has('login') ? 'error' : '' }}"
                               required
                               autocomplete="username">
                    </div>
                    @error('login')
                        <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ showPass: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-sm font-semibold text-slate-700">
                            Kata Sandi <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <a href="#" class="text-xs font-medium text-primary-600 hover:text-primary-700 transition-colors">
                            Lupa kata sandi?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </div>
                        <input :type="showPass ? 'text' : 'password'"
                               name="password"
                               id="password"
                               placeholder="Masukkan kata sandi"
                               class="input-field pl-10 pr-10 {{ $errors->has('password') ? 'error' : '' }}"
                               required
                               autocomplete="current-password">
                        <button type="button"
                                @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors">
                            <template x-if="!showPass">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </template>
                            <template x-if="showPass">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600">
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2.5">
                    <input type="checkbox"
                           name="remember"
                           id="remember"
                           class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-sm text-slate-600 cursor-pointer select-none">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary" :disabled="submitting">
                    <template x-if="submitting">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </template>
                    <template x-if="!submitting">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                    </template>
                    <span x-text="submitting ? 'Memproses...' : 'Masuk ke SIPRA'"></span>
                </button>
            </form>

            {{-- Hint credentials --}}
            <div class="mt-6 rounded-xl border border-slate-100 bg-slate-50 p-4">
                <p class="mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Akun Demo</p>
                <div class="space-y-1.5" x-data>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-bold text-primary-700">Admin</span>
                            <span class="ml-2 text-xs text-slate-600">admin@sipra.sch.id</span>
                        </div>
                        <span class="text-xs font-mono text-slate-400">admin123</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Siswa</span>
                            <span class="ml-2 text-xs text-slate-600">0051234567 (NISN)</span>
                        </div>
                        <span class="text-xs font-mono text-slate-400">siswa123</span>
                    </div>
                </div>
            </div>

            {{-- Footer note --}}
            <p class="mt-6 text-center text-xs text-slate-400">
                Dengan masuk, Anda menyetujui ketentuan penggunaan platform SIPRA.
            </p>
        </div>
    </div>
</div>

<script>
function loginPage() {
    return {
        role: '{{ old("role", "siswa") }}',
        submitting: false,
    }
}
</script>
</body>
</html>
