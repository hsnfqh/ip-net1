<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user ditemukan, seeder notifikasi dilewati.');
            return;
        }

        $samples = [
            [
                'title' => 'Task Baru Ditugaskan',
                'message' => 'Anda mendapatkan task baru dari Lead Engineer.',
                'url' => route('tasks.index'),
                'is_read' => false,
            ],
            [
                'title' => 'Deadline Mendekat',
                'message' => 'Salah satu task Anda akan jatuh tempo dalam 2 hari.',
                'url' => route('tasks.index'),
                'is_read' => false,
            ],
            [
                'title' => 'Status Task Diperbarui',
                'message' => 'Task "Instalasi Jaringan Lantai 2" berhasil diubah menjadi In Progress.',
                'url' => route('tasks.index'),
                'is_read' => true,
            ],
            [
                'title' => 'Jadwal Baru Ditambahkan',
                'message' => 'Ada jadwal kerja baru untuk minggu ini.',
                'url' => route('schedules.index'),
                'is_read' => true,
            ],
        ];

        foreach ($users as $user) {
            foreach ($samples as $sample) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $sample['title'],
                    'message' => $sample['message'],
                    'url' => $sample['url'],
                    'is_read' => $sample['is_read'],
                ]);
            }
        }

        $this->command->info('Dummy notifikasi berhasil dibuat untuk ' . $users->count() . ' user.');
    }
}