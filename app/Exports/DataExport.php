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
        return array_merge($this->existingData, $this->newData);
    }
}

