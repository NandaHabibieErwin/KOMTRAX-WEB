<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\ExcelModel;

use Illuminate\Http\Request;

class UnitTrackerControllerse extends Controller
{
    public function UploadExcelData(Request $request)
    {
        \Log::info('Uploading file:', ['file' => $request->file('file')]);


       //Disabled for now, since for some reason it wont detect as xlsx
      /*  $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);*/

        $path = $request->file('file')->store('public/excel');

    $file = $request->file('file');
    $originalName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();


    $path = $file->storeAs('excel', $originalName, 'public');


    $excel = new ExcelModel();
    $excel->filename = $originalName;
    $excel->filepath = $path;
    $excel->save();

        return response()->json(['message' => 'File uploaded successfully.', 'file_id' => $excel->id], 200);
    }

    public function getExcelFile($id)
    {
        $file = ExcelModel::findOrFail($id);
        $fileUrl = Storage::url($file->filepath);

        return response()->json(['file_path' => $fileUrl], 200);
    }

    public function downloadExcelFile($id)
    {
        $file = ExcelModel::findOrFail($id);

        return Storage::download($file->filepath, $file->filename);
    }
    public function GetLatestExcelFile()
    {
        $file = ExcelModel::latest()->first();

        if ($file) {
            $fileUrl = Storage::url($file->filepath);
            return response()->json([
                'file_path' => $fileUrl,
                'file_id' => $file->id,
                'filename' => $file->filename
            ], 200);
        } else {
            \Log::info('No files found in the database.');
            return response()->json(['message' => 'No file uploaded yet.'], 404);
        }
    }
    public function getAllExcelFiles()
{
    $files = ExcelModel::all();

    if ($files->isEmpty()) {
        return response()->json([], 200); // Return an empty array
    }

    $fileData = $files->map(function ($file) {
        return [
            'file_id' => $file->id,
            'filename' => $file->filename,
            'file_path' => Storage::url($file->filepath),
        ];
    });

    return response()->json($fileData, 200); // Return as an array of files
}
    public function ReadExcelData(Request $request)
    {
        $file = ExcelModel::latest()->first();

        if ($file) {
            $fileUrl = Storage::url($file->filepath);
            return response()->json([
                'filepath' => $fileUrl,
                'fileid' => $file->id,
                'filename' => $file->filename
            ], 200);
        } else {
            \Log::info('No files found in the database.');
            return response()->json(['message' => 'No file uploaded yet.'], 401);
        }
    }
}
