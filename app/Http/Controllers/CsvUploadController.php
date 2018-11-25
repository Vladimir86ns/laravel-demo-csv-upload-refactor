<?php

namespace App\Http\Controllers;

use App\CustomerInformation;
use App\Http\Requests\UploadCsvRequest;

class CsvUploadController extends Controller
{

    /**
     * Number of rows how much must be included in csv file.
     * Default should be 2. First is header.
     */
    const MINIMUM_CSV_ROWS = 2;

    /**
     * Mapping header names, which must be included in csv file,
     * and how fields name are saved in DB.
     */
    const CONTACTS_COLUMN_MAP = [
        'First Name' => 'first_name',
        'Last Name' => 'last_name',
        'Middle Name' => 'middle_name',
        'Gender' => 'gender',
        'Birth Day' => 'birth_day',
        'Country' => 'country',
        'City' => 'city',
        'Address' => 'address',
        'Address Number' => 'address_number',
        'Email' => 'email',
        'Contact Phone' => 'contact_phone'
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function uploadCsv()
    {
        return view('upload-csv');
    }

    /**
     * Save csv file in DB.
     *
     * @param UploadCsvRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function saveCsv(UploadCsvRequest $request)
    {
        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $this->validateCsvFields($data);
        $csvValues = $this->prepareCsvDataForSaving($data);

        return $this->saveInDB($csvValues);
    }

    /**
     * Validate header of csv file to check is it valid.
     *
     * @param array $data
     */
    private function validateCsvFields(array $data)
    {
        $isValidCsv = array_diff(array_keys(self::CONTACTS_COLUMN_MAP), $data[0]);
        if (!empty($isValidCsv)) {
            abort(406, 'CSV is missing columns!');
        }
    }

    /**
     * Save CSV file in DB.
     *
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function prepareCsvDataForSaving(array $data)
    {
        $header = $data[0];
        $columns = $this->getColumnsForDB($header);
        $csvData = array_slice($data, 1);
        $csvValues = [];

        foreach ($csvData as $row) {
            array_push($csvValues, $this->fetchColumnData($columns, $row));
        }

        return $csvValues;
    }

    /**
     * Get column names based on header for saving in Database
     *
     * @param array $header Array containing the CSV header
     * @return array $columns
     */
    private function getColumnsForDB(array $header)
    {
        $map = self::CONTACTS_COLUMN_MAP;
        $columns = [];
        $lowerCaseHeader = [];

        foreach ($map as $key => $value) {
            $lowerCaseHeader[strtolower($key)] = $value;
        }

        foreach ($header as $columnName) {
            if (!empty($columnName)) {
                $columnName = trim($columnName);
                $columns[] = $lowerCaseHeader[strtolower($columnName)] ?? $columnName;
            }
        }

        return $columns;
    }

    /**
     * Fetch column data.
     *
     * @param array $columns
     * @param array $columnData
     *
     * @return array
     */
    private function fetchColumnData(array $columns, array $columnData)
    {
        $values = [];

        foreach ($columns as $key => $column) {
            $values[$column] = !empty($columnData[$key]) ? $columnData[$key] : null;
        }

        return $values;
    }

    /**
     * Save csv value in DB
     *
     * @param array $csvValue
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function saveInDB(array $csvValue)
    {
        CustomerInformation::insert($csvValue);
        return view('upload-csv-success');
    }
}
