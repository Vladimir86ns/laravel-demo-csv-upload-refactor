<?php

namespace App\Service\CsvUpload;

class CsvUploadValidationService
{
    /**
     * Validate header of csv file to check is it valid.
     *
     * @param string $path
     */
    public function validateCsvFields(string $path, array $csvColumnMap)
    {
        $data = array_map('str_getcsv', file($path));
        $isValidCsv = array_diff(array_keys($csvColumnMap), $data[0]);
        if (!empty($isValidCsv)) {
            abort(406, 'CSV is missing columns!');
        }
    }

    /**
     * Validate how much csv has rows.
     *
     * @param array $data
     * @param int   $minimumRows
     */
    public function validateColumns(array $data, int $minimumRows)
    {
        if ($minimumRows > count($data)) {
            abort(406, 'Csv must have at least ' .  $minimumRows . ' row(s)!');
        }
    }
}
