<?php
// database/seeders/DummyDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Lead Engineer
        $lead = User::create([
            'name' => 'Rangga Saputra',
            'email' => 'rangga.saputra@ipnetwork.co.id',
            'password' => Hash::make('password123'),
            'phone' => '0812-3456-7890',
            'position' => 'Lead Engineer',
            'status' => 'Active',
        ]);
        $lead->assignRole('Lead Engineer');

        // Create Engineers
        $engineers = [
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.n@ipnetwork.co.id',
                'password' => Hash::make('password123'),
                'phone' => '0813-1122-3344',
                'position' => 'Network Engineer',
                'status' => 'Active',
                'role' => 'Engineer L1',
            ],
            [
                'name' => 'Dimas Prakoso',
                'email' => 'dimas.p@ipnetwork.co.id',
                'password' => Hash::make('password123'),
                'phone' => '0821-5566-7788',
                'position' => 'Field Technician',
                'status' => 'Active',
                'role' => 'Engineer L2',
            ],
            [
                'name' => 'Sinta Wulandari',
                'email' => 'sinta.w@ipnetwork.co.id',
                'password' => Hash::make('password123'),
                'phone' => '0856-2233-4455',
                'position' => 'Network Engineer',
                'status' => 'Active',
                'role' => 'Engineer L1',
            ],
            [
                'name' => 'Bayu Kusuma',
                'email' => 'bayu.k@ipnetwork.co.id',
                'password' => Hash::make('password123'),
                'phone' => '0878-9900-1122',
                'position' => 'Field Technician',
                'status' => 'Active',
                'role' => 'Engineer L2',
            ],
            [
                'name' => 'Rani Oktaviani',
                'email' => 'rani.o@ipnetwork.co.id',
                'password' => Hash::make('password123'),
                'phone' => '0813-4455-6677',
                'position' => 'Network Engineer',
                'status' => 'Inactive',
                'role' => 'Engineer L1',
            ],
        ];

        $engineerModels = [];
        foreach ($engineers as $data) {
            $role = $data['role'];
            unset($data['role']);
            $user = User::create($data);
            $user->assignRole($role);
            $engineerModels[] = $user;
        }

        // Create Projects
        $projects = [
            [
                'name' => 'Instalasi Fiber Optik - Gedung BCA Thamrin',
                'client' => 'PT Bank Central Asia',
                'location' => 'Jakarta Pusat',
                'description' => 'Pemasangan jalur fiber optik backbone untuk 12 lantai perkantoran.',
                'start_date' => '2026-06-01',
                'deadline' => '2026-08-15',
                'status' => 'On Progress',
                'created_by' => $lead->id,
            ],
            [
                'name' => 'Maintenance Backbone - RS Siloam Kebon Jeruk',
                'client' => 'RS Siloam',
                'location' => 'Jakarta Barat',
                'description' => 'Pemeliharaan rutin jaringan tulang punggung rumah sakit.',
                'start_date' => '2026-07-10',
                'deadline' => '2026-07-30',
                'status' => 'On Progress',
                'created_by' => $lead->id,
            ],
            [
                'name' => 'Upgrade Bandwidth - Mall Kelapa Gading',
                'client' => 'Summarecon Group',
                'location' => 'Jakarta Utara',
                'description' => 'Peningkatan kapasitas bandwidth area komersial.',
                'start_date' => '2026-05-05',
                'deadline' => '2026-06-20',
                'status' => 'Completed',
                'created_by' => $lead->id,
            ],
            [
                'name' => 'Rollout WiFi Corporate - Menara BTPN',
                'client' => 'Bank BTPN',
                'location' => 'Jakarta Selatan',
                'description' => 'Penggelaran akses WiFi korporat menyeluruh.',
                'start_date' => '2026-08-01',
                'deadline' => '2026-09-25',
                'status' => 'Planning',
                'created_by' => $lead->id,
            ],
        ];

        foreach ($projects as $data) {
            Project::create($data);
        }

        // Create Tasks
        $tasks = [
            [
                'title' => 'Tarik kabel fiber lantai 5-8',
                'project_id' => 1,
                'engineer_id' => $engineerModels[0]->id,
                'priority' => 'High',
                'status' => 'In Progress',
                'deadline' => '2026-08-05',
                'progress' => 60,
                'attachments' => 2,
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Konfigurasi core switch utama',
                'project_id' => 1,
                'engineer_id' => $engineerModels[1]->id,
                'priority' => 'High',
                'status' => 'Assigned',
                'deadline' => '2026-08-07',
                'progress' => 0,
                'attachments' => 0,
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Testing redundansi jalur backbone',
                'project_id' => 2,
                'engineer_id' => $engineerModels[0]->id,
                'priority' => 'Medium',
                'status' => 'Waiting Review',
                'deadline' => '2026-07-22',
                'progress' => 90,
                'attachments' => 3,
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Ganti patch panel rusak',
                'project_id' => 2,
                'engineer_id' => $engineerModels[3]->id,
                'priority' => 'Low',
                'status' => 'Completed',
                'deadline' => '2026-07-18',
                'progress' => 100,
                'attachments' => 4,
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Survey lokasi pemasangan AP',
                'project_id' => 4,
                'engineer_id' => $engineerModels[2]->id,
                'priority' => 'Medium',
                'status' => 'Assigned',
                'deadline' => '2026-08-10',
                'progress' => 0,
                'attachments' => 0,
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Dokumentasi topologi jaringan',
                'project_id' => 3,
                'engineer_id' => $engineerModels[0]->id,
                'priority' => 'Low',
                'status' => 'Completed',
                'deadline' => '2026-06-18',
                'progress' => 100,
                'attachments' => 1,
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Instalasi rack server lantai 2',
                'project_id' => 1,
                'engineer_id' => $engineerModels[3]->id,
                'priority' => 'High',
                'status' => 'In Progress',
                'deadline' => '2026-08-03',
                'progress' => 35,
                'attachments' => 1,
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Update firmware access point',
                'project_id' => 2,
                'engineer_id' => $engineerModels[1]->id,
                'priority' => 'Medium',
                'status' => 'In Progress',
                'deadline' => '2026-07-25',
                'progress' => 50,
                'attachments' => 0,
                'created_by' => $lead->id,
            ],
        ];

        foreach ($tasks as $data) {
            Task::create($data);
        }

        // Create Schedules
        $schedules = [
            [
                'title' => 'Instalasi kabel lantai 5',
                'project_id' => 1,
                'engineer_id' => $engineerModels[0]->id,
                'date' => '2026-08-10',
                'start_time' => '08:00',
                'end_time' => '12:00',
                'location' => 'Gedung BCA Thamrin',
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Konfigurasi switch core',
                'project_id' => 1,
                'engineer_id' => $engineerModels[1]->id,
                'date' => '2026-08-10',
                'start_time' => '09:00',
                'end_time' => '15:00',
                'location' => 'Gedung BCA Thamrin',
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Cek redundansi backbone',
                'project_id' => 2,
                'engineer_id' => $engineerModels[0]->id,
                'date' => '2026-08-11',
                'start_time' => '10:00',
                'end_time' => '13:00',
                'location' => 'RS Siloam Kebon Jeruk',
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Survey lokasi AP',
                'project_id' => 4,
                'engineer_id' => $engineerModels[2]->id,
                'date' => '2026-08-11',
                'start_time' => '13:00',
                'end_time' => '16:00',
                'location' => 'Menara BTPN',
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Ganti patch panel',
                'project_id' => 2,
                'engineer_id' => $engineerModels[3]->id,
                'date' => '2026-08-12',
                'start_time' => '08:30',
                'end_time' => '11:00',
                'location' => 'RS Siloam Kebon Jeruk',
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Instalasi rack server',
                'project_id' => 1,
                'engineer_id' => $engineerModels[3]->id,
                'date' => '2026-08-13',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'location' => 'Gedung BCA Thamrin',
                'created_by' => $lead->id,
            ],
            [
                'title' => 'Update firmware AP',
                'project_id' => 2,
                'engineer_id' => $engineerModels[1]->id,
                'date' => '2026-08-14',
                'start_time' => '09:00',
                'end_time' => '11:30',
                'location' => 'RS Siloam Kebon Jeruk',
                'created_by' => $lead->id,
            ],
        ];

        foreach ($schedules as $data) {
            Schedule::create($data);
        }
    }
}