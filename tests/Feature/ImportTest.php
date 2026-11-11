<?php

namespace Tests\Feature;

use App\Models\Ordinance;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = "ordinance_number,title,subject,date_passed,author_sponsor,summary\n";

    private function csvFile(string $content, string $name = 'batch.csv'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $content);
    }

    public function test_guests_cannot_use_the_import_pages(): void
    {
        $this->get('/admin/ordinances/import')->assertRedirect('/login');
        $this->get('/admin/resolutions/import')->assertRedirect('/login');
        $this->post('/admin/ordinances/import', ['file' => $this->csvFile(self::HEADER)])->assertRedirect('/login');
    }

    public function test_csv_file_imports_many_ordinances(): void
    {
        $user = User::factory()->create();
        $csv = self::HEADER
            . "2026-101,Environment Ordinance,Environment,2026-01-15,Hon. A,First summary\n"
            . "2026-102,Agriculture Ordinance,Agriculture,2026-02-10,Hon. B,\n";

        $this->actingAs($user)->post('/admin/ordinances/import', ['file' => $this->csvFile($csv)])
            ->assertRedirect(route('admin.ordinances.index'));

        $this->assertDatabaseCount('ordinances', 2);
        $this->assertDatabaseHas('ordinances', ['ordinance_number' => '2026-102', 'subject' => 'Agriculture']);
    }

    public function test_csv_file_imports_many_resolutions(): void
    {
        $user = User::factory()->create();
        $csv = "resolution_number,title,subject,date_passed,author_sponsor\n"
            . "R-1,First Resolution,Health,2026-01-15,Hon. A\n"
            . "R-2,Second Resolution,Education,2026-02-10,Hon. B\n";

        $this->actingAs($user)->post('/admin/resolutions/import', ['file' => $this->csvFile($csv)])
            ->assertRedirect(route('admin.resolutions.index'));

        $this->assertSame(2, Resolution::count());
    }

    public function test_column_names_are_flexible(): void
    {
        $user = User::factory()->create();
        $csv = "Ordinance Number,Title,Subject,Date Passed,Author/Sponsor\n"
            . "2026-201,Flexible Headers,Health,2026-01-15,Hon. A\n";

        $this->actingAs($user)->post('/admin/ordinances/import', ['file' => $this->csvFile($csv)])
            ->assertRedirect(route('admin.ordinances.index'));

        $this->assertDatabaseHas('ordinances', ['ordinance_number' => '2026-201']);
    }

    public function test_one_bad_row_stops_the_whole_import(): void
    {
        $user = User::factory()->create();
        $csv = self::HEADER
            . "2026-301,Good Row,Health,2026-01-15,Hon. A,\n"
            . "2026-302,Bad Date Row,Health,2026-02-30,Hon. B,\n"
            . "2026-303,,Health,2026-01-15,Hon. C,\n";

        $this->actingAs($user)->post('/admin/ordinances/import', ['file' => $this->csvFile($csv)])
            ->assertSessionHas('import_errors');

        $this->assertDatabaseCount('ordinances', 0);
    }

    public function test_numbers_that_already_exist_or_repeat_in_the_file_are_rejected(): void
    {
        $user = User::factory()->create();
        Ordinance::create([
            'ordinance_number' => '2026-401',
            'title'            => 'Existing',
            'subject'          => 'Health',
            'date_passed'      => '2026-01-01',
            'author_sponsor'   => 'Hon. A',
        ]);

        $csv = self::HEADER
            . "2026-401,Already in system,Health,2026-01-15,Hon. A,\n"
            . "2026-402,New one,Health,2026-01-15,Hon. A,\n"
            . "2026-402,Repeated in file,Health,2026-01-15,Hon. A,\n";

        $this->actingAs($user)->post('/admin/ordinances/import', ['file' => $this->csvFile($csv)])
            ->assertSessionHas('import_errors');

        $this->assertDatabaseCount('ordinances', 1);
    }

    public function test_file_with_missing_columns_is_rejected(): void
    {
        $user = User::factory()->create();
        $csv = "ordinance_number,title\n2026-501,Only two columns\n";

        $this->actingAs($user)->post('/admin/ordinances/import', ['file' => $this->csvFile($csv)])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('ordinances', 0);
    }

    public function test_other_file_types_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/admin/ordinances/import', ['file' => $this->csvFile('hello', 'notes.pdf')])
            ->assertSessionHasErrors('file');
    }

    public function test_xlsx_file_with_real_excel_dates_is_imported(): void
    {
        $user = User::factory()->create();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ordinance_number');
        $sheet->setCellValue('B1', 'title');
        $sheet->setCellValue('C1', 'subject');
        $sheet->setCellValue('D1', 'date_passed');
        $sheet->setCellValue('E1', 'author_sponsor');
        $sheet->setCellValue('A2', 'X-1');
        $sheet->setCellValue('B2', 'Excel Ordinance');
        $sheet->setCellValue('C2', 'Health');
        $sheet->setCellValue('D2', Date::PHPToExcel(new \DateTime('2026-01-15')));
        $sheet->getStyle('D2')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $sheet->setCellValue('E2', 'Hon. Excel');

        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        (new Xlsx($spreadsheet))->save($path);

        $file = new UploadedFile($path, 'batch.xlsx', null, null, true);

        $this->actingAs($user)->post('/admin/ordinances/import', ['file' => $file])
            ->assertRedirect(route('admin.ordinances.index'));

        $this->assertSame('2026-01-15', Ordinance::first()->date_passed->format('Y-m-d'));

        unlink($path);
    }

    public function test_template_can_be_downloaded(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/ordinances/import/template');

        $response->assertOk();
        $this->assertStringContainsString('ordinance_number', $response->streamedContent());
    }
}
