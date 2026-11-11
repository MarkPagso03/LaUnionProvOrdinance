<?php

namespace Database\Seeders;

use App\Models\Ordinance;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Creates the default administrator and a few SAMPLE (fictional) records.
     * Safe to run more than once: existing records are not duplicated.
     */
    public function run(): void
    {
        // Default administrator account
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        // Sample ordinances (fictional data for testing the search)
        $ordinances = [
            ['2025-001', 'Ordinance Establishing the Provincial Environment Protection and Waste Management Program', 'Environment', '2025-02-10', 'Hon. Maria Santos'],
            ['2025-002', 'Ordinance Promoting Sustainable Agriculture and Organic Farming in the Province', 'Agriculture', '2025-03-18', 'Hon. Juan Dela Cruz'],
            ['2025-003', 'Ordinance Regulating Tourism Activities in Coastal Areas', 'Tourism', '2025-06-05', 'Hon. Ana Reyes'],
            ['2025-004', 'Ordinance Creating the Provincial Disaster Risk Reduction Fund', 'Disaster Management', '2025-09-22', 'Hon. Pedro Ramos'],
            ['2026-001', 'Ordinance Strengthening Environmental Education in Public Schools', 'Education', '2026-01-27', 'Hon. Maria Santos'],
        ];

        foreach ($ordinances as [$number, $title, $subject, $date, $author]) {
            Ordinance::firstOrCreate(
                ['ordinance_number' => $number],
                [
                    'title'          => $title,
                    'subject'        => $subject,
                    'date_passed'    => $date,
                    'author_sponsor' => $author,
                    'summary'        => 'Sample record for testing.',
                ]
            );
        }

        // Sample resolutions (fictional data for testing the search)
        $resolutions = [
            ['2025-001', 'Resolution Commending the Provincial Agriculture Office for Outstanding Performance', 'Agriculture', '2025-01-20', 'Hon. Juan Dela Cruz'],
            ['2025-002', 'Resolution Requesting National Assistance for Flood Control Projects', 'Disaster Management', '2025-04-14', 'Hon. Ana Reyes'],
            ['2025-003', 'Resolution Authorizing the Governor to Sign a Memorandum of Agreement on Environment Monitoring', 'Environment', '2025-07-08', 'Hon. Pedro Ramos'],
            ['2025-004', 'Resolution Supporting Farmers Cooperatives and Agricultural Training Programs', 'Agriculture', '2025-10-13', 'Hon. Maria Santos'],
            ['2026-001', 'Resolution Declaring a State of Calamity Due to Typhoon Damage', 'Disaster Management', '2026-02-16', 'Hon. Ana Reyes'],
        ];

        foreach ($resolutions as [$number, $title, $subject, $date, $author]) {
            Resolution::firstOrCreate(
                ['resolution_number' => $number],
                [
                    'title'          => $title,
                    'subject'        => $subject,
                    'date_passed'    => $date,
                    'author_sponsor' => $author,
                    'summary'        => 'Sample record for testing.',
                ]
            );
        }
    }
}
