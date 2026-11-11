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

        // Actual sample data from the api "https://sp-api.launion.gov.ph/lisearch/search"
        $ordinances = [
            ['556-2026', 'Amending Provincial Appropriation Ordinance No. 004-2026 to Correct the Location of the Gumot-Nagcolaran Small Scale Irrigation Project from Naguilian to Rosario, La Union', 'Amendment', '2026-09-22', 'Teresita "Tess" O. Garcia'],

            ['558-2026', 'Granting a One-Day Temporary Exemption from Service Fees for Service Records and Certificates of Employment Issued to Provincial Government Employees on September 28, 2026, in Connection with the 126th Philippine Civil Service Anniversary and AGKAYSA! Appreciation Week', 'Revenue Code', '2026-09-22', 'Eulogio Clarence Martin P. De Guzman III'],

            ['555-2026', 'Abolishing the Plantilla Position of Senior Administrative Assistant IV and Creating the Position of Administrative Officer III (Records Officer II) in the Office of the Sangguniang Panlalawigan Secretariat, Province of La Union', 'Abolition of Positions', '2026-09-08', 'Ruperto A. Rillera Jr.'],

            ['553-2026', 'River Restoration and Waterway Rehabilitation Ordinance of the Province of La Union', 'River Restoration and Waterway Rehabilitation Ordinance', '2026-08-18', 'Harold Dave E. Sibuma'],

            ['552-2026', 'Providing for the Rules of Procedure in Administrative Cases Before the Sangguniang Panlalawigan', 'Rules of Procedure', '2026-08-11', 'Teresita "Tess" O. Garcia'],

            ['551-2026', 'Officially Naming the Provincial Government-Owned Lot Adjacent to the Speaker Pro Tempore Francisco I. Ortega Convention Center as the La Union AGKAYSA Complex', 'Naming of Landmark / Building', '2026-07-21', 'Gerard G. Ostrea'],

            ['549-2026', 'Creating Four Positions of Administrative Aide III (Utility Worker II) and Three Positions of Administrative Aide III (Driver I) Under the Plantilla of the Office of the Sangguniang Panlalawigan', 'Creation of Position', '2026-07-14', 'Ruperto A. Rillera Jr.'],

            ['550-2026', 'Abolishing Fourteen Co-Terminus Positions of Private Secretary I and Fourteen Co-Terminus Positions of Administrative Aide III Under the Office of the Sangguniang Panlalawigan, Province of La Union', 'Abolition of Positions', '2026-07-14', 'Ruperto A. Rillera Jr.'],

            ['547-2026', 'Amending Sections 5, 7, 9, 11 and 12 of Ordinance No. 025-2012 Creating the Ten Outstanding Policemen in the Province of La Union Award, as Amended by Ordinance No. 119-2017', 'Amendment', '2026-06-30', 'Gerard G. Ostrea'],

            ['548-2026', 'Amending Chapter VII on Disposal of Supplies and Property of Provincial Ordinance No. 174-2019, Otherwise Known as the Administrative Code of La Union', 'Amendment', '2026-06-30', 'Ernesto V. Rafon'],

            ['544-2026', 'Regulating the Establishment and Operations of Travel and Tour Agencies, Prohibiting Unauthorized Tourism Operations in the Province of La Union, and Providing Penalties for Violations', 'Travel and Tour Agencies', '2026-06-09', 'Gerard G. Ostrea'],

            ['545-2026', 'Institutionalizing the Feel La Union Tourism Brand as the Official Tourism Identity of the Province of La Union, Adopting Its Official Logo and Visual Identity System, and Prescribing Penalties for Unauthorized Use', 'Institutionalizing', '2026-06-09', 'Alyssa Kristine B. Sibuma'],

            ['546-2026', 'Amending the Title and Sections of Provincial Ordinance No. 374-2022 Institutionalizing the Financial Assistance Given to the Pederasyon ng May Kapansanan ng La Union (PMKLU)', 'Amendment', '2026-06-09', 'Aaron Kyle M. Pinzon'],

            ['542-2026', 'Establishing the La Union Inclusive Employment and Administrative Awards Program, Encouraging Establishments, Investors and Contractors to Prioritize Qualified Bona Fide Residents', 'Award Program', '2026-05-26', 'Miguel Corleone B. Magsaysay'],

            ['543-2026', 'Enacting the Local Energy Code of the Province of La Union, Providing a Comprehensive Framework for Energy Efficiency and Conservation and Promoting Sustainable Energy Development', 'Enacting', '2026-05-26', 'Gerard G. Ostrea'],
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

        // Actual sample data from the api "https://sp-api.launion.gov.ph/lisearch/search"
        $resolutions = [
            ['3205-2026', 'Approving the Joint Committee Report on the 2026 Revenue Code of the City of San Fernando', 'Adopt Committee Report / Joint Committee Report', '2026-11-09', 'Eulogio Clarence Martin P. De Guzman III'],
            ['3206-2026', 'Declaring City Ordinance No. 26-002 of San Fernando Invalid for Review', 'Municipal Ordinance', '2026-11-09', 'Eulogio Clarence Martin P. De Guzman III'],
            ['3172-2026', 'Authorizing the Governor to Enter into a Deed of Donation for Hospital Equipment and Materials', 'Deed of Donation (DOD)', '2026-11-03', 'Ruperto A. Rillera Jr.'],
            ['3121-2026', 'Approving the Joint Committee Report on the Creation of Two Plantilla Positions in San Juan', 'Adopt Committee Report / Joint Committee Report', '2026-11-03', 'Ruperto A. Rillera Jr.'],
            ['3122-2026', 'Declaring Municipal Ordinance No. 10-2026 of San Juan Valid for Review', 'Municipal Ordinance', '2026-11-03', 'Ruperto A. Rillera Jr.'],
            ['3023-2026', 'Approving the Joint Committee Report on the Certified List of Delinquencies of Nineteen LGUs', 'Adopt Committee Report / Joint Committee Report', '2026-10-27', 'Miguel Corleone B. Magsaysay'],
            ['3024-2026', 'Taking Note of the Certified List of Delinquencies of Nineteen LGUs', 'Takes Note', '2026-10-27', 'Miguel Corleone B. Magsaysay'],
            ['3027-2026', 'Approving the Joint Committee Report on Identifying the Ownership of Gas Stations in the Province', 'Adopt Committee Report / Joint Committee Report', '2026-10-27', 'Miguel Corleone B. Magsaysay'],
            ['3028-2026', 'Taking Note of the Provincial Assessor Memorandum on Identifying Gas Station Ownership', 'Takes Note', '2026-10-27', 'Miguel Corleone B. Magsaysay'],
            ['3029-2026', 'Approving the Joint Committee Report on the PNR Coordination and Strategic Alignment Meeting', 'Adopt Committee Report / Joint Committee Report', '2026-10-27', 'Miguel Corleone B. Magsaysay'],
            ['3039-2026', 'Approving the Joint Committee Report on Temporarily Suspending the Local Legislative Award', 'Adopt Committee Report / Joint Committee Report', '2026-10-27', 'Ernesto V. Rafon'],
            ['3040-2026', 'Temporarily Suspending the Local Legislative Award and Approving Alternative Activities', 'Temporarily Suspending', '2026-10-27', 'Danielle Bianca G. Ortega'],
            ['3047-2026', 'Approving the Joint Committee Report on SP Administrative Case No. 02-2026', 'Adopt Committee Report / Joint Committee Report', '2026-10-27', 'Ernesto V. Rafon'],
            ['3048-2026', 'Dismissing Without Prejudice SP Administrative Case No. 02-2026', 'Dismiss without Prejudice', '2026-10-27', 'Ernesto V. Rafon'],
            ['3050-2026', 'Authorizing the Governor to Sign an MOA with the Municipality of Agoo for Emergency Evacuation Use', 'Memorandum of Agreement (MOA)', '2026-10-27', 'Alyssa Kristine B. Sibuma'],
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
