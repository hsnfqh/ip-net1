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
        <!-- Notifications Dropdown -->
        <div class="relative" x-data="notificationDropdown()" x-init="init()">
            <button @click="toggleDropdown()" class="wms-iconbtn p-2 text-wms-ink-700 relative inline-flex focus:outline-none" title="Pemberitahuan">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <template x-if="unreadCount > 0">
                    <span class="absolute top-1.5 right-1.5 w-[8px] h-[8px] rounded-full bg-red-600 border-2 border-white animate-pulse"></span>
                </template>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-cloak
                 @click.outside="open = false"
                 class="absolute right-0 mt-2 w-[320px] sm:w-[360px] bg-white border border-wms-line rounded-xl shadow-lg z-50 overflow-hidden"
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95">
                 
                <div class="px-4 py-3 border-b border-wms-line bg-gray-50 flex items-center justify-between">
                    <span class="text-[13px] font-bold text-wms-ink-900">Notifikasi</span>
                    <template x-if="unreadCount > 0">
                        <button @click="markAllRead()" class="text-[11px] font-semibold text-red-600 hover:underline">
                            Tandai semua dibaca
                        </button>
                    </template>
                </div>

                <div class="max-h-[280px] overflow-y-auto divide-y divide-gray-100">
                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-6 text-center text-[12.5px] text-wms-ink-500">
                            Tidak ada notifikasi baru.
                        </div>
                    </template>
                    <template x-for="notif in notifications" :key="notif.id">
                        <a :href="notif.url || '#'" 
                           @click="open = false"
                           class="block px-4 py-3 hover:bg-gray-50/70 transition-colors"
                           :class="!notif.is_read ? 'bg-red-50/20' : ''">
                            <div class="flex gap-2">
                                <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" :class="!notif.is_read ? 'bg-red-600' : 'bg-transparent'"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[12.5px] font-semibold text-wms-ink-900 leading-snug" x-text="notif.title"></div>
                                    <div class="text-[11.5px] text-wms-ink-500 mt-0.5" x-text="notif.message"></div>
                                    <div class="text-[10.5px] text-wms-ink-400 mt-1" x-text="notif.time_ago"></div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>

                <div class="border-t border-wms-line bg-gray-50 text-center">
                    <a href="{{ route('notifications.index') }}" class="block py-2 text-[12px] font-semibold text-wms-ink-700 hover:text-red-600 transition-colors">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="hidden sm:block w-px h-[26px] bg-wms-line"></div>

        <!-- User Profile Link -->
        <a href="{{ route('profile.show') }}" class="flex items-center gap-[9px] hover:opacity-85 transition-opacity" title="Lihat Profil Saya">
            <x-avatar :name="$user->name" size="34" tone="#C81E2C" />
            <div class="hidden sm:block">
                <div class="text-[13px] font-semibold text-wms-ink-900 leading-tight">{{ $user->name }}</div>
                <div class="text-[11.5px] text-wms-ink-500">{{ $user->role_label }}</div>
            </div>
        </a>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        if (window.hasRegisteredNotificationDropdown) return;
        window.hasRegisteredNotificationDropdown = true;
        
        Alpine.data('notificationDropdown', () => ({
            open: false,
            notifications: [],
            unreadCount: 0,
            pollingInterval: null,

            init() {
                this.fetchNotifications();
                this.pollingInterval = setInterval(() => {
                    this.fetchNotifications();
                }, 30000);
            },

            async fetchNotifications() {
                try {
                    let response = await fetch('{{ route('notifications.latest') }}');
                    if (response.ok) {
                        let data = await response.json();
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            },

            toggleDropdown() {
                this.open = !this.open;
                if (this.open) {
                    this.fetchNotifications();
                }
            },

            async markAllRead() {
                try {
                    let response = await fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    if (response.ok) {
                        this.fetchNotifications();
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            }
        }));
    });
</script>