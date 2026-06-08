<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'Employee',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee account created successfully.',
            'data' => $user
        ], 201);
    }
}
