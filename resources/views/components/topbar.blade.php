@php
    $user = auth()->user();

    // Hitung notifikasi belum dibaca dengan aman — kalau tabel notifications
    // belum ada (migration belum dijalankan), badge cuma nggak tampil, bukan error.
    $unreadNotifCount = 0;
    if ($user) {
        try {
            $unreadNotifCount = \App\Models\Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        } catch (\Throwable $e) {
            $unreadNotifCount = 0;
        }
    }
@endphp

<div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-[28px] sm:py-[18px] border-b border-wms-line bg-white sticky top-0 z-10">

    <!-- Judul: menyusut & terpotong dengan elipsis kalau kepanjangan -->
    <div class="min-w-0">
        <h1 class="font-display text-[18px] sm:text-[21px] font-semibold text-wms-ink-900 tracking-[-0.2px] truncate">{{ $title }}</h1>
    </div>

    <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
        <!-- Notifications -->
        <a href="{{ route('notifications.index') }}" class="wms-iconbtn p-2 text-wms-ink-700 relative inline-flex">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            @if($unreadNotifCount > 0)
            <span class="absolute top-1.5 right-1.5 w-[7px] h-[7px] rounded-full bg-wms-red-600 shadow-[0_0_0_2px_#FFFFFF]"></span>
            @endif
        </a>

        <!-- Divider -->
        <div class="hidden sm:block w-px h-[26px] bg-wms-line"></div>

        <!-- User -->
        <div class="flex items-center gap-[9px]">
            <x-avatar :name="$user->name" size="34" tone="#C81E2C" />
            <div class="hidden sm:block">
                <div class="text-[13px] font-semibold text-wms-ink-900 leading-tight">{{ $user->name }}</div>
                <div class="text-[11.5px] text-wms-ink-500">{{ $user->role_label }}</div>
            </div>
        </div>
    </div>
</div>