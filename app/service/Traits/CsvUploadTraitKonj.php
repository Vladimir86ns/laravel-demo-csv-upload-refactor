<?php

namespace App\Traits;

trait CsvUpload
{
    /**
     * Get column names based on header
     *
     * @param array $header
     * @param array $map
     *
     * @return array
     */
    private function getColumnsForDB(array $header, array $map)
    {
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
     * Save CSV file in DB.
     *
     * @param array $data
     * @return array
     */
    private function prepareCsvDataForSaving(array $data, $map)
    {
        $header = $data[0];
        $columns = $this->getColumnsForDB($header, $map);

        $csvData = array_slice($data, 1);
        $csvValues = [];

        foreach ($csvData as $row) {
            array_push($csvValues, $this->fetchColumnData($columns, $row));
        }

        return $csvValues;
    }

}
