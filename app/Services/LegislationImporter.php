<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

/**
 * Reads a CSV / Excel file and saves many ordinances (or resolutions) at once.
 * It is used by both OrdinanceController and ResolutionController.
 *
 * The import is ALL-OR-NOTHING: if any row has a problem, nothing is saved,
 * and the user gets a list of the rows to fix.
 */
class LegislationImporter
{
    private const MAX_ROWS = 1000;

    // Accepted column names in the first row of the file (after lowercasing and
    // replacing spaces/symbols with "_"), mapped to our internal names.
    private const HEADER_ALIASES = [
        'number'            => 'number',
        'no'                => 'number',
        'ordinance_number'  => 'number',
        'resolution_number' => 'number',
        'title'             => 'title',
        'subject'           => 'subject',
        'date_passed'       => 'date_passed',
        'date'              => 'date_passed',
        'author_sponsor'    => 'author_sponsor',
        'author'            => 'author_sponsor',
        'sponsor'           => 'author_sponsor',
        'summary'           => 'summary',
        'remarks'           => 'summary',
    ];

    private const REQUIRED_COLUMNS = ['number', 'title', 'subject', 'date_passed', 'author_sponsor'];

    private string $modelClass;
    private string $numberColumn;
    private string $label;

    /**
     * @param string $modelClass   e.g. Ordinance::class
     * @param string $numberColumn e.g. 'ordinance_number'
     * @param string $label        e.g. 'ordinance' (used in messages)
     */
    public function __construct(string $modelClass, string $numberColumn, string $label)
    {
        $this->modelClass = $modelClass;
        $this->numberColumn = $numberColumn;
        $this->label = $label;
    }

    /**
     * Returns: ['imported' => int, 'errors' => [['row' => 3, 'messages' => [...]], ...], 'fileError' => string|null]
     */
    public function import(UploadedFile $file): array
    {
        // 1. Read the file
        try {
            $rows = $this->readFile($file);
        } catch (InvalidArgumentException $e) {
            return $this->result(0, [], $e->getMessage());
        } catch (Throwable $e) {
            return $this->result(0, [], 'The file could not be read. Please use a valid .csv, .xlsx or .xls file (see the template).');
        }

        // 2. Check every row. Nothing is saved if there is any problem.
        $errors = $this->checkRows($rows);
        if (count($errors) > 0) {
            return $this->result(0, $errors, null);
        }

        // 3. Save all rows together (if one fails, none are saved)
        try {
            $model = $this->modelClass;

            DB::transaction(function () use ($rows, $model) {
                foreach ($rows as $data) {
                    $model::create([
                        $this->numberColumn => $data['number'],
                        'title'             => $data['title'],
                        'subject'           => $data['subject'],
                        'date_passed'       => $data['date_passed'],
                        'author_sponsor'    => $data['author_sponsor'],
                        'summary'           => $data['summary'] ?? null,
                    ]);
                }
            });
        } catch (QueryException $e) {
            return $this->result(0, [], 'The records could not be saved. A ' . $this->label . ' number may already exist. Nothing was imported.');
        }

        return $this->result(count($rows), [], null);
    }

    /**
     * A small CSV file that the user can download, fill in, and upload.
     */
    public function downloadTemplate()
    {
        $header = [$this->numberColumn, 'title', 'subject', 'date_passed', 'author_sponsor', 'summary'];
        $sample = ['2026-100', 'Sample title of the ' . $this->label, 'Environment', '2026-01-15', 'Hon. Sample Person', 'Optional summary'];

        return response()->streamDownload(function () use ($header, $sample) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header, ',', '"', '\\');
            fputcsv($out, $sample, ',', '"', '\\');
            fclose($out);
        }, $this->label . '-import-template.csv', ['Content-Type' => 'text/csv']);
    }

    // ------------------------------------------------------------------
    // Reading the file
    // ------------------------------------------------------------------

    /**
     * Returns the data rows as [spreadsheetRowNumber => ['number' => ..., 'title' => ..., ...]]
     */
    private function readFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $readerType = match ($extension) {
            'csv'   => 'Csv',
            'xlsx'  => 'Xlsx',
            'xls'   => 'Xls',
            default => throw new InvalidArgumentException('Only .csv, .xlsx and .xls files can be imported.'),
        };

        $reader = IOFactory::createReader($readerType);

        // For CSV: detect the text encoding (Excel often saves CSV as ANSI, not UTF-8)
        if ($reader instanceof Csv) {
            $reader->setInputEncoding('guess');
        }

        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();

        $lastRow = $sheet->getHighestDataRow();
        $lastColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        // Row 1 must be the column names
        $columns = [];   // column number => internal name
        for ($col = 1; $col <= $lastColumn; $col++) {
            $name = $this->normalizeHeader((string) $sheet->getCell([$col, 1])->getCalculatedValue());

            if (isset(self::HEADER_ALIASES[$name]) && !in_array(self::HEADER_ALIASES[$name], $columns, true)) {
                $columns[$col] = self::HEADER_ALIASES[$name];
            }
        }

        $missing = array_diff(self::REQUIRED_COLUMNS, $columns);
        if (count($missing) > 0) {
            $names = array_map(fn ($c) => $c === 'number' ? $this->numberColumn : $c, $missing);
            throw new InvalidArgumentException('Missing column(s) in row 1: ' . implode(', ', $names) . '. Please use the template.');
        }

        if ($lastRow - 1 > self::MAX_ROWS) {
            throw new InvalidArgumentException('The file has too many rows. The maximum is ' . self::MAX_ROWS . ' rows per import.');
        }

        // Rows 2 and below are the data
        $rows = [];
        for ($row = 2; $row <= $lastRow; $row++) {
            $data = [];
            $hasData = false;

            foreach ($columns as $col => $name) {
                $cell = $sheet->getCell([$col, $row]);
                $value = $cell->getCalculatedValue();

                // A real Excel date is stored as a number. Convert it to YYYY-MM-DD.
                if ($name === 'date_passed' && is_numeric($value) && Date::isDateTime($cell)) {
                    $value = Date::excelToDateTimeObject($value)->format('Y-m-d');
                }

                $value = trim((string) $value);

                if ($value === '') {
                    $value = null;
                } else {
                    $hasData = true;
                }

                $data[$name] = $value;
            }

            // Skip rows that are completely empty
            if ($hasData) {
                $rows[$row] = $data;
            }
        }

        $spreadsheet->disconnectWorksheets();

        if (count($rows) === 0) {
            throw new InvalidArgumentException('The file has no data rows.');
        }

        return $rows;
    }

    // "Author/Sponsor" -> "author_sponsor", "Date Passed" -> "date_passed"
    private function normalizeHeader(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);

        return trim($text, '_');
    }

    // ------------------------------------------------------------------
    // Checking the rows (same rules as the Add/Edit forms)
    // ------------------------------------------------------------------

    private function checkRows(array $rows): array
    {
        $model = $this->modelClass;

        // Numbers that are already saved in the database
        $numbersInFile = array_filter(array_column($rows, 'number'));
        $existing = [];
        foreach ($model::whereIn($this->numberColumn, $numbersInFile)->pluck($this->numberColumn) as $number) {
            $existing[mb_strtolower($number)] = true;
        }

        $seenInFile = [];   // number => first row where it appeared
        $errors = [];

        foreach ($rows as $rowNumber => $data) {
            $validator = Validator::make($data, $this->rules(), [], $this->attributes());
            $messages = $validator->errors()->all();

            if ($data['number'] !== null) {
                $key = mb_strtolower($data['number']);

                if (isset($existing[$key])) {
                    $messages[] = 'The ' . $this->label . ' number "' . $data['number'] . '" already exists.';
                } elseif (isset($seenInFile[$key])) {
                    $messages[] = 'The ' . $this->label . ' number "' . $data['number'] . '" is repeated (first used in row ' . $seenInFile[$key] . ').';
                }

                $seenInFile[$key] = $seenInFile[$key] ?? $rowNumber;
            }

            if (count($messages) > 0) {
                $errors[] = ['row' => $rowNumber, 'messages' => $messages];
            }
        }

        return $errors;
    }

    private function rules(): array
    {
        return [
            'number'         => ['required', 'string', 'max:50'],
            'title'          => ['required', 'string', 'max:500'],
            'subject'        => ['required', 'string', 'max:255'],
            'date_passed'    => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'author_sponsor' => ['required', 'string', 'max:255'],
            'summary'        => ['nullable', 'string', 'max:5000'],
        ];
    }

    private function attributes(): array
    {
        return [
            'number'         => $this->label . ' number',
            'date_passed'    => 'date passed (use YYYY-MM-DD)',
            'author_sponsor' => 'author/sponsor',
        ];
    }

    private function result(int $imported, array $errors, ?string $fileError): array
    {
        return ['imported' => $imported, 'errors' => $errors, 'fileError' => $fileError];
    }
}
