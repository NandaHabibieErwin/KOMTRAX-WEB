<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnrollModel;

class EnrollController extends Controller
{
    public function AddEnroll(Request $request)
    {
        $validated = $request->validate([
            'nama_filter' => 'required|string',
            'machine' => 'nullable|string',
        ]);

        $validated['IdUser'] = auth()->user()->id;
        $enroll = EnrollModel::create($validated);

        return response()->json(['success' => true, 'enroll' => $enroll], 201);
    }

    public function EditEnroll(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'nama_filter' => 'required|string',
            'machine' => 'nullable|string',
            'target' => 'nullable|numeric',
        ]);

        $enroll = EnrollModel::find($validated['id']);
        if (!$enroll) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        $enroll->update([
            'nama_filter' => $validated['nama_filter'],
            'machine' => $validated['machine'],
            'target' => $validated['target'],
        ]);

        return response()->json(['success' => true, 'enroll' => $enroll], 201);
    }

    public function DeleteEnroll(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
        ]);

        $enroll = EnrollModel::find($validated['id']);
        if (!$enroll) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        $enroll->delete();
    }

    public function ReadEnroll()
    {

        $EnrollData = EnrollModel::where('idUser', auth()->user()->id)->get();
        foreach($EnrollData as $data){
        $data->machine = preg_split('/\s*,\s*/', $data->machine);
    }

        return response()->json([
            'filters' => $EnrollData
        ]);
    }
}
