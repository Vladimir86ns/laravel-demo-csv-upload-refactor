<?php

namespace App\Service\CsvUpload;

use App\Contact;
use App\CustomerInformation;
use App\Traits\CsvUpload;
use Maatwebsite\Excel\Facades\Excel;

class CsvUploadService
{
    use CsvUpload;

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

    /*
     * \App\Service\CsvUpload\CsvUploadValidationService
     */
    private $validationService;
    /**
     * CsvUploadService constructor.
     */
    public function __construct(CsvUploadValidationService $csvUploadValidationService)
    {
        $this->validationService = $csvUploadValidationService;
    }

    /**
     * Save data from csv file if header is market as false.
     *
     * @param string $path
     */
    public function saveCsvFile(string $path)
    {
        $this->validationService->validateCsvFields($path, self::CONTACTS_COLUMN_MAP);
        $data = array_map('str_getcsv', file($path));

        $this->validationService->validateColumns($data, self::MINIMUM_CSV_ROWS);
        $csvValues = $this->prepareCsvDataForSaving($data, self::CONTACTS_COLUMN_MAP);

        $this->saveInDB($csvValues);
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
    }
}
