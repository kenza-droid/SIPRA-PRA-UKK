@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Daftar seluruh siswa terdaftar di SIPRA')

@section('content')

{{-- ── Filter ──────────────────────────────────────────────── --}}
<x-table-filter :action="route('admin.siswa')">
    <div class="sm:col-span-2">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, NISN, atau kelas..."
                   class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
        </div>
    </div>
</x-table-filter>

{{-- ── Action & Result info ─────────────────────────────────── --}}
<div class="mt-4 mb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <p class="text-sm text-slate-500">
        Menampilkan <strong class="text-slate-700">{{ $siswaList->total() }}</strong> siswa terdaftar
    </p>
    <button type="button"
            @click="$dispatch('open-modal', { id: 'create-siswa-modal' })"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-primary-700 active:scale-[0.98] transition-all duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z"/>
        </svg>
        <span>Tambah Akun Siswa</span>
    </button>
</div>

@if($siswaList->isEmpty())
    <div class="flex flex-col items-center">
        <x-empty-state
            title="Tidak ada siswa ditemukan"
            description="Coba ubah kata kunci pencarian Anda atau tambahkan akun siswa baru."
            icon="users" />
        <button type="button"
                @click="$dispatch('open-modal', { id: 'create-siswa-modal' })"
                class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-primary-700 active:scale-[0.98] transition-all duration-150">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Tambah Akun Siswa Baru</span>
        </button>
    </div>
@else
    <div x-data="{ loadingSiswa: null }">
        {{-- Desktop table --}}
        <div class="hidden overflow-x-auto rounded-2xl bg-white border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)] md:block">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Siswa</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">NISN</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Kelas</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500">No. Telepon</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500">Total Aspirasi</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500">Selesai</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    @foreach($siswaList as $i => $siswa)
                        <tr class="group hover:bg-slate-50/80 transition-colors duration-150">
                            <td class="px-5 py-4 text-xs text-slate-400">
                                {{ $siswaList->firstItem() + $i }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $siswa->avatar_url }}" alt="{{ $siswa->name }}"
                                         class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-100">
                                    <p class="text-sm font-semibold text-slate-800 group-hover:text-primary-700 transition-colors">{{ $siswa->name }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs text-slate-600">{{ $siswa->nisn ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $siswa->class ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500">
                                {{ $siswa->email }}
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600 font-mono">
                                {{ $siswa->phone ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($siswa->complaints_count > 0)
                                    <span class="inline-flex items-center justify-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100 tabular-nums">
                                        {{ $siswa->complaints_count }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-300">0</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($siswa->selesai_count > 0)
                                    <span class="inline-flex items-center justify-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100 tabular-nums">
                                        {{ $siswa->selesai_count }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-300">0</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('admin.complaints.index', ['siswa' => $siswa->id]) }}"
                                   @click="loadingSiswa = {{ $siswa->id }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-xs hover:border-primary-200 hover:bg-indigo-50 hover:text-primary-700 active:scale-[0.98] transition-all duration-150">
                                    <svg x-show="loadingSiswa === {{ $siswa->id }}" x-cloak class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    <span>Lihat Aspirasi</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="space-y-3 md:hidden">
            @foreach($siswaList as $siswa)
                <div class="rounded-2xl bg-white p-4 border border-slate-200/75 shadow-[0_1px_2px_rgba(15,23,42,0.03)] hover:border-slate-300/80 transition-colors">
                    <div class="flex items-center gap-3">
                        <img src="{{ $siswa->avatar_url }}" alt="{{ $siswa->name }}"
                             class="h-11 w-11 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-100">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-800">{{ $siswa->name }}</p>
                            <p class="truncate text-xs text-slate-400">{{ $siswa->class ?? '—' }} · {{ $siswa->email }}</p>
                            @if($siswa->phone)
                                <p class="truncate text-[11px] font-mono text-slate-500 mt-0.5">{{ $siswa->phone }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-slate-50 p-2">
                            <p class="text-lg font-bold text-slate-700 tabular-nums">{{ $siswa->complaints_count }}</p>
                            <p class="text-[10px] text-slate-400">Total</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50/80 p-2">
                            <p class="text-lg font-bold text-emerald-600 tabular-nums">{{ $siswa->selesai_count }}</p>
                            <p class="text-[10px] text-slate-400">Selesai</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-2">
                            <p class="truncate text-xs font-semibold text-slate-600 pt-1 font-mono">{{ $siswa->nisn ?? '—' }}</p>
                            <p class="text-[10px] text-slate-400">NISN</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <a href="{{ route('admin.complaints.index', ['siswa' => $siswa->id]) }}"
                           @click="loadingSiswa = {{ $siswa->id }}"
                           class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 active:scale-[0.98] transition-all duration-150">
                            <svg x-show="loadingSiswa === {{ $siswa->id }}" x-cloak class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span>Lihat Aspirasi →</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">{{ $siswaList->links() }}</div>
@endif

{{-- ── Modal Tambah Akun Siswa ────────────────────────────── --}}
<x-modal id="create-siswa-modal" title="Tambah Akun Siswa Baru" size="lg">
    <div x-data="{ submitting: false, showPassword: false }"
         x-init="@if(isset($errors) && $errors->any()) $dispatch('open-modal', { id: 'create-siswa-modal' }) @endif">
        <form method="POST" action="{{ route('admin.siswa.store') }}" @submit="submitting = true" class="space-y-4">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Lengkap Siswa <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255"
                       placeholder="cth: Ahmad Fauzi Pratama"
                       class="w-full rounded-xl border {{ (isset($errors) && $errors->has('name')) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-white' }} px-3.5 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                @error('name')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- NISN --}}
                <div>
                    <label for="nisn" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        NISN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" required maxlength="20"
                           placeholder="cth: 0071234567"
                           class="w-full rounded-xl border font-mono {{ (isset($errors) && $errors->has('nisn')) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-white' }} px-3.5 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <p class="mt-1 text-[11px] text-slate-400">Dapat digunakan siswa untuk login.</p>
                    @error('nisn')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kelas / Jurusan --}}
                <div>
                    <label for="class" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kelas / Jurusan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="class" id="class" value="{{ old('class') }}" required maxlength="50"
                           placeholder="cth: XII RPL 1"
                           class="w-full rounded-xl border {{ (isset($errors) && $errors->has('class')) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-white' }} px-3.5 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <p class="mt-1 text-[11px] text-slate-400">Rombel saat ini.</p>
                    @error('class')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email & No. Telepon --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="255"
                           placeholder="cth: ahmad.fauzi@sekolah.sch.id"
                           class="w-full rounded-xl border {{ (isset($errors) && $errors->has('email')) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-white' }} px-3.5 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        No. Telepon / WA
                    </label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" maxlength="20"
                           placeholder="cth: 081234567890"
                           class="w-full rounded-xl border font-mono {{ (isset($errors) && $errors->has('phone')) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-white' }} px-3.5 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Kata Sandi & Konfirmasi --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required minlength="6"
                               placeholder="Min. 6 karakter"
                               class="w-full rounded-xl border {{ (isset($errors) && $errors->has('password')) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200 bg-white' }} px-3.5 py-2.5 pr-10 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <button type="button" @click="showPassword = !showPassword"
                                tabindex="-1"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <svg x-show="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Ulangi Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required minlength="6"
                           placeholder="Ulangi kata sandi"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-xs text-slate-500 leading-relaxed">
                <span class="font-semibold text-slate-700">Catatan:</span> Akun siswa ini dapat langsung digunakan untuk masuk (login) ke SIPRA menggunakan NISN atau Email siswa beserta kata sandi di atas.
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button"
                        @click="$dispatch('close-modal', { id: 'create-siswa-modal' })"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-all duration-150">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-primary-700 active:scale-[0.98] disabled:opacity-70 transition-all duration-150">
                    <svg x-show="!submitting" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan Akun Siswa'">Simpan Akun Siswa</span>
                </button>
            </div>
        </form>
    </div>
</x-modal>

@endsection
