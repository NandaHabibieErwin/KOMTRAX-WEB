<?php

namespace App\Http\Controllers;
use App\Exports\DataExport;
use App\Exports\RowHandlerArray;
use App\Exports\RowHandlerExport;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\ExcelModel;

use Illuminate\Http\Request;

class UnitTrackerController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('Enroll', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function ReadMainExcel()
    {
        $filePath = 'excel/UnitTracker.xlsx';

        if (Storage::disk('public')->exists($filePath)) {
            $path = Storage::disk('public')->path($filePath);
            $data = Excel::toArray([], $path);

            return response()->json([
                'data' => $data,
                'filepath' => Storage::url($filePath),
                'filename' => basename($filePath),
            ], 200);
        }

        return response()->json(['message' => 'File not found'], 404);
    }

    public function UploadExcelData(Request $request)
    {
        \Log::info('Uploading file:', ['file' => $request->file('file')]);

        //Disabled for now, this cant detect xlsx file correctly, causing it to always fail uploading

        /*  $request->validate([
              'file' => 'required|mimes:xlsx,xls',
          ]);*/
        $filePath = 'excel/UnitTracker.xlsx';

        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'UnitTracker.xlsx file not found.'], 404);
        }

        $path = Storage::disk('public')->path($filePath);
        $existingData = Excel::toArray([], $path);


        $uploadedFile = $request->file('file');
        $uploadedData = Excel::toArray([], $uploadedFile);


        if (empty($uploadedData) || empty($uploadedData[0])) {
            return response()->json(['message' => 'Uploaded Excel file contains no data.'], 400);
        }

        /*
        Appended Column
        0 = Machine Type (A)
        1 = Manufacturer (B)
        2 = Model (C)
        3 = Type (D)
        4 = Serial Number (E)
        67 = Working Hour (BP)
        68 = Actual Working Hour (BQ)
        120 = Period From (DQ)
        121 = Period To (DR)
        */
        $columnsToAppend = [0, 1, 2, 3, 4, 67, 68, 120, 121];
        $sheets = [];

        foreach ($uploadedData as $sheetIndex => $sheet) {
            foreach ($sheet as $rowIndex => $row) {
                $currentSheetIndex = $rowIndex;
                $newRowData = [];
                foreach ($columnsToAppend as $colIndex) {
                    $value = isset($row[$colIndex]) ? $row[$colIndex] : null;

                    // Convert Working Hour (67) and Actual Working Hour (68) to dates
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                        $newRowData[] = \Carbon\Carbon::createFromFormat('m/d/Y', $value)->toDateString();

                    } else {
                        $newRowData[] = $value;
                    }
                }

                if (isset($existingData[$currentSheetIndex])) {
                    $sheetData = $existingData[$currentSheetIndex];
                } else {
                    $sheetData = [];
                }

                $sheets[$currentSheetIndex] = new DataExport($sheetData, [$newRowData]);
            }
        }

        $multiSheetExport = new RowHandlerExport($sheets);


        Excel::store(new RowHandlerExport($sheets), $filePath, 'public');

        $originalName = $uploadedFile->getClientOriginalName();
        \Log::info('Data appended from file: ' . $originalName);

        $this->DeleteDuplicateData();

        return response()->json(['message' => 'Data appended successfully.'], 200);
    }

    public function DeleteDuplicateData()
    {
        $filePath = 'excel/UnitTracker.xlsx';

        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'UnitTracker.xlsx file not found.'], 404);
        }

        $path = Storage::disk('public')->path($filePath);
        $existingData = Excel::toArray([], $path);


        $filteredData = [];

        foreach ($existingData as $sheetIndex => $sheet) {
            $dateFroms = [];
            $filteredSheet = [];

            foreach ($sheet as $rowIndex => $row) {

                $dateFrom = isset($row[7]) ? $row[7] : null;


                if ($dateFrom && in_array($dateFrom, $dateFroms)) {

                    \Log::info("Duplicate DateFrom found and removed", ['dateFrom' => $dateFrom, 'rowIndex' => $rowIndex]);
                    continue;
                }

                $dateFroms[] = $dateFrom;
                $filteredSheet[] = $row;
            }


            $filteredData[$sheetIndex] = new RowHandlerArray($filteredSheet);
        }
        $multiSheetExport = new RowHandlerExport($filteredData);
        Excel::store($multiSheetExport, $filePath, 'public');

        return response()->json(['message' => 'Duplicate rows based on DateFrom removed within each sheet successfully.'], 200);
    }




    public function getExcelFile($id)
    {
        $file = ExcelModel::findOrFail($id);
        $fileUrl = Storage::url($file->filepath);

        return response()->json(['file_path' => $fileUrl], 200);
    }
}


