<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Program;
use App\Models\ProgramDocument;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ScmDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@scm.corp'],
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'role' => 'Admin SCM',
                'status' => 'approved',
                'division' => 'Divisi Supply Chain Management',
                'initials' => 'BS',
                'password' => Hash::make('password123'),
                'approved_at' => Carbon::now()->subDays(30)
            ]
        );

        $auditor = User::updateOrCreate(
            ['email' => 'auditor@pajak.corp'],
            [
                'name' => 'Siti Rahmawati',
                'phone' => '081224290502',
                'role' => 'Tim Pajak',
                'status' => 'approved',
                'division' => 'Tax & Compliance Audit',
                'initials' => 'SR',
                'password' => Hash::make('password123'),
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(20)
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@scm.corp'],
            [
                'name' => 'Hendra Wijaya',
                'phone' => '081298765432',
                'role' => 'Staf SCM',
                'status' => 'approved',
                'division' => 'Operasional Logistik SCM',
                'initials' => 'HW',
                'password' => Hash::make('password123'),
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(15)
            ]
        );

        $pendingUser = User::updateOrCreate(
            ['email' => 'reza25022003@gmail.com'],
            [
                'name' => 'Reza Pratama',
                'phone' => '081234567899',
                'role' => 'Tim Pajak',
                'status' => 'pending', // Menunggu ACC Admin
                'division' => 'Tax & Compliance Audit',
                'initials' => 'RP',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Seed Initial Programs
        $samplePrograms = [
            [
                'id' => '1',
                'title' => 'Program Supply Chain Optimization',
                'supplier' => 'PT Cipta Logistik Nusantara',
                'category' => 'Logistik',
                'invoice_no' => 'INV/CLP/2025/1016',
                'dpp_amount' => 337837838,
                'ppn_amount' => 37162162,
                'total_amount' => 375000000,
                'due_date' => '2025-06-16',
                'status' => 'Lengkap',
                'docs' => [
                    ['id' => 'doc-101', 'type' => 'invoice', 'file_name' => 'invoice-clp-1016.pdf', 'file_size' => '1.4 MB'],
                    ['id' => 'doc-102', 'type' => 'faktur', 'file_name' => 'faktur-pajak-clp-1016.pdf', 'file_size' => '820 KB'],
                    ['id' => 'doc-103', 'type' => 'memo', 'file_name' => 'mou-optimasi-clp-2025.pdf', 'file_size' => '3.1 MB'],
                ]
            ],
            [
                'id' => '2',
                'title' => 'Pengadaan Armada Pendingin Logistik',
                'supplier' => 'PT Samudera Perkasa Abadi',
                'category' => 'Logistik',
                'invoice_no' => 'INV/SPA/2025/0842',
                'dpp_amount' => 828828829,
                'ppn_amount' => 91171171,
                'total_amount' => 920000000,
                'due_date' => '2025-06-12',
                'status' => 'Perlu Tindakan',
                'docs' => [
                    ['id' => 'doc-201', 'type' => 'invoice', 'file_name' => 'inv-spa-0842-armada.pdf', 'file_size' => '1.8 MB'],
                    ['id' => 'doc-202', 'type' => 'memo', 'file_name' => 'mou-pengadaan-armada-spa.pdf', 'file_size' => '2.4 MB'],
                ]
            ],
            [
                'id' => '3',
                'title' => 'Implementasi WMS Cold Storage',
                'supplier' => 'PT Integrasi Sistem Solusindo',
                'category' => 'Teknologi',
                'invoice_no' => 'INV/ISS/2025/0421',
                'dpp_amount' => 216216216,
                'ppn_amount' => 23783784,
                'total_amount' => 240000000,
                'due_date' => '2025-06-08',
                'status' => 'Lengkap',
                'docs' => [
                    ['id' => 'doc-301', 'type' => 'invoice', 'file_name' => 'inv-wms-iss-0421.pdf', 'file_size' => '940 KB'],
                    ['id' => 'doc-302', 'type' => 'faktur', 'file_name' => 'faktur-pajak-iss-0421.pdf', 'file_size' => '670 KB'],
                    ['id' => 'doc-303', 'type' => 'memo', 'file_name' => 'kontrak-spk-wms-iss.pdf', 'file_size' => '4.2 MB'],
                ]
            ],
            [
                'id' => '4',
                'title' => 'Pengadaan Pallet Rack Heavy Duty',
                'supplier' => 'PT Baja Unggul Konstruksi',
                'category' => 'Material',
                'invoice_no' => 'INV/BUK/2025/1190',
                'dpp_amount' => 468468468,
                'ppn_amount' => 51531532,
                'total_amount' => 520000000,
                'due_date' => '2025-06-05',
                'status' => 'Perlu Tindakan',
                'docs' => [
                    ['id' => 'doc-401', 'type' => 'invoice', 'file_name' => 'inv-buk-pallet-1190.pdf', 'file_size' => '1.1 MB'],
                    ['id' => 'doc-402', 'type' => 'faktur', 'file_name' => 'faktur-pajak-buk-1190.pdf', 'file_size' => '780 KB'],
                ]
            ],
            [
                'id' => '5',
                'title' => 'Jasa Konsultansi Kepatuhan PPN & Bea',
                'supplier' => 'Kantor Konsultan Pajak Tan & Rekan',
                'category' => 'Jasa',
                'invoice_no' => 'INV/KKP-TR/2025/0078',
                'dpp_amount' => 135135135,
                'ppn_amount' => 14864865,
                'total_amount' => 150000000,
                'due_date' => '2025-05-30',
                'status' => 'Lengkap',
                'docs' => [
                    ['id' => 'doc-501', 'type' => 'invoice', 'file_name' => 'inv-konsultan-tan-0078.pdf', 'file_size' => '650 KB'],
                    ['id' => 'doc-502', 'type' => 'faktur', 'file_name' => 'faktur-pajak-tan-0078.pdf', 'file_size' => '510 KB'],
                    ['id' => 'doc-503', 'type' => 'memo', 'file_name' => 'engagement-letter-pajak-tan.pdf', 'file_size' => '1.9 MB'],
                ]
            ]
        ];

        foreach ($samplePrograms as $pData) {
            $docs = $pData['docs'];
            unset($pData['docs']);

            $prog = Program::updateOrCreate(
                ['id' => $pData['id']],
                $pData
            );

            foreach ($docs as $d) {
                ProgramDocument::updateOrCreate(
                    ['id' => $d['id']],
                    [
                        'program_id' => $prog->id,
                        'type' => $d['type'],
                        'file_name' => $d['file_name'],
                        'file_size' => $d['file_size'],
                        'uploaded_at' => Carbon::now()->subDays(rand(1, 15))
                    ]
                );
            }
        }
    }
}
