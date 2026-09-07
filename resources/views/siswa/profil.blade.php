@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')

<div class="mx-auto max-w-2xl space-y-6">

    {{-- ── Profile header card ──────────────────────────────── --}}
    <div class="overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-indigo-500 p-6 shadow-lg shadow-primary-200/30">
        <div class="flex items-center gap-5">
            <div class="relative flex-shrink-0">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                     class="h-20 w-20 rounded-2xl object-cover ring-4 ring-white/30 shadow-xl">
                <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-400 ring-2 ring-white">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-indigo-200 text-sm">{{ $user->email }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if($user->class)
                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white ring-1 ring-white/30">
                            Kelas {{ $user->class }}
                        </span>
                    @endif
                    @if($user->nisn)
                        <span class="rounded-full bg-white/20 px-3 py-1 font-mono text-xs font-semibold text-white ring-1 ring-white/30">
                            NISN: {{ $user->nisn }}
                        </span>
                    @endif
                    <span class="rounded-full bg-emerald-400/30 px-3 py-1 text-xs font-semibold text-emerald-100 ring-1 ring-emerald-300/30">
                        Siswa
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Update Profil ────────────────────────────────────── --}}
    <x-card title="Informasi Profil" subtitle="Perbarui nama dan email Anda">
        <form method="POST" action="{{ route('siswa.profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-form-input name="name" label="Nama Lengkap" :required="true">
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('name') border-red-400 @enderror">
                </x-form-input>

                <x-form-input name="email" label="Alamat Email" :required="true">
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('email') border-red-400 @enderror">
                </x-form-input>
            </div>

            {{-- Read-only fields --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NISN</label>
                    <input type="text" value="{{ $user->nisn ?? '—' }}" disabled
                           class="w-full rounded-xl border-[1.5px] border-slate-100 bg-slate-100 px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed font-mono">
                    <p class="mt-1 text-xs text-slate-400">NISN tidak dapat diubah oleh siswa.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kelas</label>
                    <input type="text" value="{{ $user->class ?? '—' }}" disabled
                           class="w-full rounded-xl border-[1.5px] border-slate-100 bg-slate-100 px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                    <p class="mt-1 text-xs text-slate-400">Kelas dikelola oleh admin.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-card>

    {{-- ── Ganti Password ───────────────────────────────────── --}}
    <x-card title="Keamanan Akun" subtitle="Perbarui kata sandi Anda secara berkala">
        <form method="POST" action="{{ route('siswa.profile.password') }}"
              class="space-y-5" x-data="{ showCurr: false, showNew: false, showConf: false }">
            @csrf
            @method('PUT')

            <x-form-input name="current_password" label="Kata Sandi Saat Ini" :required="true">
                <div class="relative">
                    <input :type="showCurr ? 'text' : 'password'"
                           name="current_password"
                           placeholder="Masukkan kata sandi lama"
                           class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 pr-10 text-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('current_password') border-red-400 @enderror">
                    <button type="button" @click="showCurr = !showCurr"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-show="!showCurr">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-show="showCurr" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
            </x-form-input>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-form-input name="password" label="Kata Sandi Baru" :required="true" hint="Minimal 8 karakter.">
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'"
                               name="password"
                               placeholder="Kata sandi baru"
                               class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 pr-10 text-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('password') border-red-400 @enderror">
                        <button type="button" @click="showNew = !showNew"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-show="!showNew">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </x-form-input>

                <x-form-input name="password_confirmation" label="Konfirmasi Kata Sandi" :required="true">
                    <div class="relative">
                        <input :type="showConf ? 'text' : 'password'"
                               name="password_confirmation"
                               placeholder="Ulangi kata sandi baru"
                               class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 pr-10 text-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100">
                        <button type="button" @click="showConf = !showConf"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-show="!showConf">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </x-form-input>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-slate-900 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </x-card>

    {{-- ── Akun info ────────────────────────────────────────── --}}
    <x-card title="Informasi Akun">
        <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            @foreach([
                ['label' => 'Bergabung',    'value' => $user->created_at->format('d M Y')],
                ['label' => 'Role',         'value' => 'Siswa'],
                ['label' => 'Status',       'value' => 'Aktif'],
            ] as $dl)
                <div class="rounded-xl bg-slate-50 p-3">
                    <dt class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $dl['label'] }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $dl['value'] }}</dd>
                </div>
            @endforeach
        </dl>
    </x-card>
</div>

@endsection
