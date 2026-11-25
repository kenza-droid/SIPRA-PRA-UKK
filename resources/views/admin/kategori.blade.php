@extends('layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Kategori Aspirasi')
@section('page-subtitle', 'Kelola kategori untuk pengelompokan aspirasi')

@section('content')

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3"
     x-data="{
         deleteTarget: null,
         deleteName: '',
         deleting: false,
         confirmDelete(url, name) {
             this.deleteTarget = url;
             this.deleteName = name;
             this.$dispatch('open-modal', { id: 'delete-category-modal' });
         }
     }">

    {{-- ── Daftar Kategori ──────────────────────────────────── --}}
    <div class="xl:col-span-2">
        <x-card>
            <x-slot:title>Daftar Kategori</x-slot:title>
            <x-slot:action>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100">
                    {{ $categories->count() }} kategori
                </span>
            </x-slot:action>

            @if($categories->isEmpty())
                <x-empty-state title="Belum ada kategori" description="Tambahkan kategori pertama menggunakan form di samping." icon="tag" />
            @else
                <div class="space-y-2.5">
                    @foreach($categories as $cat)
                        <div class="rounded-xl border border-slate-200/75 bg-slate-50/50 transition-all duration-150 hover:border-slate-300/80 hover:bg-white"
                             x-data="{ editOpen: false, savingEdit: false }">
                            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3.5 p-4">

                                {{-- Color icon --}}
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl
                                    {{ match($cat->color) {
                                        'blue'   => 'bg-blue-50 text-blue-600 ring-1 ring-blue-100',
                                        'teal'   => 'bg-teal-50 text-teal-600 ring-1 ring-teal-100',
                                        'purple' => 'bg-purple-50 text-purple-600 ring-1 ring-purple-100',
                                        'indigo' => 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100',
                                        'green'  => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100',
                                        'cyan'   => 'bg-cyan-50 text-cyan-600 ring-1 ring-cyan-100',
                                        'yellow' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-100',
                                        'red'    => 'bg-rose-50 text-rose-600 ring-1 ring-rose-100',
                                        'orange' => 'bg-orange-50 text-orange-600 ring-1 ring-orange-100',
                                        default  => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                                    } }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                                    </svg>
                                </div>

                                {{-- Info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-800">{{ $cat->name }}</p>
                                        @if(!$cat->is_active)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Nonaktif</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $cat->description ?: '—' }}</p>
                                    <p class="mt-1 text-[11px] font-medium text-slate-400">
                                        {{ $cat->complaints_count }} aspirasi terkait
                                    </p>
                                </div>

                                {{-- Urutan + Actions --}}
                                <div class="flex flex-shrink-0 items-center gap-1.5 ml-auto">
                                    <span title="Urutan tampil" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-xs font-semibold text-slate-500 border border-slate-200/80 tabular-nums">
                                        {{ $cat->sort_order }}
                                    </span>

                                    <button type="button"
                                            @click="editOpen = !editOpen"
                                            aria-label="Edit kategori {{ $cat->name }}"
                                            title="Edit kategori"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:border-primary-200 hover:bg-indigo-50/60 hover:text-primary-600 active:scale-95 transition-all duration-150">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </button>

                                    @if($cat->complaints_count === 0)
                                        <button type="button"
                                                @click="confirmDelete('{{ route('admin.categories.destroy', $cat) }}', @js($cat->name))"
                                                aria-label="Hapus kategori {{ $cat->name }}"
                                                title="Hapus kategori"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 active:scale-95 transition-all duration-150">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Inline edit form --}}
                            <div x-show="editOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1"
                                 class="border-t border-indigo-100 bg-indigo-50/30 p-4 rounded-b-xl">
                                <p class="mb-3 text-xs font-semibold text-indigo-700">Edit Kategori</p>
                                <form method="POST" action="{{ route('admin.categories.update', $cat) }}" @submit="savingEdit = true">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama *</label>
                                            <input type="text" name="name" value="{{ $cat->name }}" required
                                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Warna *</label>
                                            <select name="color"
                                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                                @foreach(['blue','teal','purple','indigo','green','cyan','yellow','red','orange'] as $clr)
                                                    <option value="{{ $clr }}" {{ $cat->color === $clr ? 'selected' : '' }}>{{ ucfirst($clr) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Deskripsi</label>
                                            <input type="text" name="description" value="{{ $cat->description }}"
                                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="is_active" id="active_{{ $cat->id }}" value="1"
                                                   {{ $cat->is_active ? 'checked' : '' }}
                                                   class="h-4 w-4 rounded text-primary-600 focus:ring-primary-400">
                                            <label for="active_{{ $cat->id }}" class="text-xs text-slate-600">Aktif</label>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="editOpen = false"
                                                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-all duration-150">
                                                Batal
                                            </button>
                                            <button type="submit" :disabled="savingEdit"
                                                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-700 active:scale-[0.98] disabled:opacity-70 transition-all duration-150">
                                                <svg x-show="savingEdit" x-cloak class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                                <span x-text="savingEdit ? 'Menyimpan...' : 'Simpan'">Simpan</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    {{-- ── Tambah Kategori ──────────────────────────────────── --}}
    <div>
        <x-card title="Tambah Kategori Baru">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4"
                  x-data="{ creating: false }" @submit="creating = true">
                @csrf
                <x-form-input name="name" label="Nama Kategori" :required="true">
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="cth: Kantin Sekolah"
                           class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100 @error('name') border-red-400 @enderror">
                </x-form-input>

                <x-form-input name="description" label="Deskripsi">
                    <input type="text" name="description" value="{{ old('description') }}"
                           placeholder="Deskripsi singkat kategori ini"
                           class="w-full rounded-xl border-[1.5px] border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-primary-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-primary-100">
                </x-form-input>

                <x-form-input name="color" label="Warna" :required="true">
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['blue','bg-blue-400'],['teal','bg-teal-400'],['purple','bg-purple-400'],
                            ['indigo','bg-indigo-400'],['green','bg-green-400'],['cyan','bg-cyan-400'],
                            ['yellow','bg-yellow-400'],['red','bg-red-400'],['orange','bg-orange-400'],
                        ] as [$clr, $bgCls])
                            <label class="flex cursor-pointer items-center justify-center gap-1.5 rounded-xl border-2 border-transparent bg-slate-50/70 p-2.5 transition-all duration-150 has-[:checked]:border-primary-400 has-[:checked]:bg-primary-50 hover:bg-slate-100/70 active:scale-[0.97]">
                                <input type="radio" name="color" value="{{ $clr }}"
                                       {{ old('color', 'blue') === $clr ? 'checked' : '' }}
                                       class="sr-only">
                                <span class="h-4 w-4 rounded-full {{ $bgCls }}"></span>
                                <span class="text-xs font-medium text-slate-600">{{ ucfirst($clr) }}</span>
                            </label>
                        @endforeach
                    </div>
                </x-form-input>

                <button type="submit" :disabled="creating"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 active:scale-[0.98] disabled:opacity-70 transition-all duration-150 shadow-xs">
                    <svg x-show="!creating" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <svg x-show="creating" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="creating ? 'Menambahkan...' : 'Tambah Kategori'">Tambah Kategori</span>
                </button>
            </form>
        </x-card>

        {{-- Info box --}}
        <div class="mt-4 rounded-2xl border border-amber-200/80 bg-amber-50/80 p-4">
            <div class="flex gap-3">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                <div>
                    <p class="text-xs font-semibold text-amber-800">Catatan</p>
                    <p class="mt-0.5 text-xs text-amber-700 leading-relaxed">
                        Kategori yang memiliki aspirasi terkait tidak dapat dihapus. Nonaktifkan saja jika tidak ingin ditampilkan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Delete Confirmation Modal ────────────────────────── --}}
    <x-modal id="delete-category-modal" title="Hapus Kategori" size="sm">
        <p class="text-sm text-slate-600 leading-relaxed">
            Apakah kamu yakin ingin menghapus kategori <strong class="text-slate-900" x-text="deleteName"></strong>? Tindakan ini tidak dapat dibatalkan.
        </p>

        <x-slot:footer>
            <div class="flex items-center justify-end gap-2.5">
                <button type="button"
                        @click="$dispatch('close-modal', { id: 'delete-category-modal' })"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-all duration-150">
                    Batal
                </button>
                <form method="POST" :action="deleteTarget" @submit="deleting = true">
                    @csrf @method('DELETE')
                    <button type="submit" :disabled="deleting"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 active:scale-[0.98] disabled:opacity-70 transition-all duration-150">
                        <svg x-show="deleting" x-cloak class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus'">Ya, Hapus</span>
                    </button>
                </form>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
@endsection
