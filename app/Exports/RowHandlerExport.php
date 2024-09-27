<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RowHandlerExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return $this->data;
    }
}

