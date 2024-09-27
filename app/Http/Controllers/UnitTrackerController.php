<?php

namespace App\Http\Controllers;
use App\Exports\DataExport;
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
                'data' => $data,                // This will be an array of sheets, each containing its rows
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
        Compare Column, it should be the date
        Column start from 0 = A
        This prevents duplicate data
        */
        $compareColumnIndex = [120];

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
                    $newRowData[] = isset($row[$colIndex]) ? $row[$colIndex] : null;
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

        return response()->json(['message' => 'Data appended successfully.'], 200);


    }
    public function AddData()
    {

    }

    public function getExcelFile($id)
    {
        $file = ExcelModel::findOrFail($id);
        $fileUrl = Storage::url($file->filepath);

        return response()->json(['file_path' => $fileUrl], 200);
    }
}
