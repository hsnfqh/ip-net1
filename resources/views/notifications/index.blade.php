@extends('layouts.app')

@section('title', 'Semua Notifikasi')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')

    <div class="flex-1 min-w-0">
        @include('components.topbar', ['title' => 'Semua Notifikasi'])

        <div class="p-[26px] animate-fade-in">
            <div class="flex justify-end mb-3">
                @if($notifications->where('is_read', false)->isNotEmpty())
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[12.5px] font-semibold text-wms-red-600 hover:underline">
                        Tandai semua sudah dibaca
                    </button>
                </form>
                @endif
            </div>

            <div class="wms-card overflow-hidden">
                @forelse($notifications as $notif)
                <div class="flex items-start gap-3 p-4 border-t border-wms-line2 first:border-t-0 {{ !$notif->is_read ? 'bg-red-50/40' : '' }}">
                    <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ !$notif->is_read ? 'bg-wms-red-600' : 'bg-wms-line' }}"></span>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13.5px] font-medium text-wms-ink-900">{{ $notif->title }}</div>
                        <div class="text-[12px] text-wms-ink-500 mt-0.5">{{ $notif->message }}</div>
                        <div class="text-[11px] text-wms-ink-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-wms-ink-500">
                    <p class="text-[13.5px]">Belum ada notifikasi.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection