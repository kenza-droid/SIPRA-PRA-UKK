@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua pemberitahuan untuk akun Anda')

@section('content')

<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">
        {{ $notifications->total() }} notifikasi
    </p>
    @if($notifications->total() > 0)
        <form method="POST"
              action="{{ auth()->user()->isAdmin()
                  ? route('admin.notifications.read-all')
                  : route('siswa.notifications.read-all') }}">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

@if($notifications->isEmpty())
    <x-empty-state
        title="Tidak ada notifikasi"
        description="Semua notifikasi akan muncul di sini."
        icon="bell" />
@else
    <div class="space-y-2">
        @foreach($notifications as $notif)
            <div class="flex items-start gap-4 rounded-2xl border p-4 transition-all
                {{ $notif->is_read ? 'border-slate-100 bg-white' : 'border-primary-100 bg-primary-50/40 shadow-sm' }}">

                {{-- Icon --}}
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl
                    {{ match($notif->type) {
                        'success' => 'bg-emerald-100 text-emerald-600',
                        'danger'  => 'bg-red-100 text-red-600',
                        'warning' => 'bg-amber-100 text-amber-600',
                        default   => 'bg-primary-100 text-primary-600',
                    } }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        @switch($notif->icon)
                            @case('check-circle')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @break
                            @case('arrow-path')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                @break
                            @case('chart-bar')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                @break
                            @case('inbox')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/>
                                @break
                            @default
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        @endswitch
                    </svg>
                </div>

                {{-- Content --}}
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-800">{{ $notif->title }}</p>
                        @if(!$notif->is_read)
                            <span class="flex h-2 w-2 flex-shrink-0 rounded-full bg-primary-500 mt-1.5"></span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-sm text-slate-500 leading-relaxed">{{ $notif->message }}</p>
                    <p class="mt-1.5 text-xs text-slate-400">{{ $notif->created_at->diffForHumans() }}</p>
                    @if($notif->action_url)
                        <a href="{{ $notif->action_url }}"
                           class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                            Lihat Detail
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
@endif

@endsection
