<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadCsvRequest;
use App\Service\CsvUpload\CsvUploadService;

class CsvUploadController extends Controller
{
    /*
     * \App\Services\CsvUpload\CsvUploadService
     */
    private $service;

    /**
     * ImportCsvController constructor.
     */
    public function __construct(CsvUploadService $csvUploadService)
    {
        $this->service = $csvUploadService;
    }

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
        $this->service->saveCsvFile($path);

        return view('upload-csv-success');
    }
}
