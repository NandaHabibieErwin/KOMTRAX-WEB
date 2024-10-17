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

    public function EditEnroll()
    {

    }

    public function DeleteEnroll()
    {

    }

    public function ReadEnroll()
    {

        $EnrollData = EnrollModel::where('idUser', auth()->user()->id)->get();
        return response()->json([
            'filters' => $EnrollData
        ]);
    }
}
