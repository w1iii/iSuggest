<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Mail\WelcomeEmployee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'Employee');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->select('id', 'name', 'email', 'status', 'title', 'created_at')
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json($employees);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'Employee',
        ]);

        Mail::to($user)->send(new WelcomeEmployee($user));

        return response()->json([
            'success' => true,
            'message' => 'Employee account created successfully. Login details sent to their email.',
            'data' => $user
        ], 201);
    }
}
