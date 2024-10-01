<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class DataExport implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */


    protected $existingData;
    protected $newData;

    public function __construct(array $existingData, array $newData)
    {

        $this->existingData = $existingData;
        $this->newData = $newData;
    }

    public function array(): array
    {
        // Ensure we return non-empty data even if one array is empty
        if (empty($this->existingData)) {
            return $this->newData;
        }

        if (empty($this->newData)) {
            return $this->existingData;
        }

        // Merge existing data with new data
        return array_merge($this->existingData, $this->newData);
    }
}

