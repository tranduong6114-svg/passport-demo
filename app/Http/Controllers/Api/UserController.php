<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all(['id', 'name', 'email', 'created_at']);

        return response()->json([
            'data'          => $users,
            'count'         => $users->count(),
            'requested_by'  => $request->user()->only(['id', 'name', 'email']),
        ]);
    }
}