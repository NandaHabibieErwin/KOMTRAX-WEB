<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{

    public function index(Request $request)
    {
    return Inertia::render('Admin/Upload', [
        'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
        'status' => session('status'),
    ]);
    }

    public function RetrieveCustomerName()
    {
        $User = User::where('status', 'user')->pluck('name');
        return response()->json($User);


    }

}
