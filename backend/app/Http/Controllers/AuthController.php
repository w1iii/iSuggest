<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create new user with hashed password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
        ]);

        // Return 201 Created response with success message and user data
        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['sometimes', 'string', 'in:employee,admin'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($request->role) {
            $isEmployeePortal = $request->role === 'employee';
            $isAdminPortal = $request->role === 'admin';
            $userIsAdmin = in_array($user->role, ['Administrator', 'Super Administrator']);
            $userIsEmployee = $user->role === 'Employee';

            if ($isAdminPortal && !$userIsAdmin) {
                throw ValidationException::withMessages([
                    'role' => ['This account does not have admin access. Please use the Employee Portal.'],
                ]);
            }

            if ($isEmployeePortal && !$userIsEmployee) {
                throw ValidationException::withMessages([
                    'role' => ['This account does not have employee access. Please use the Admin Portal.'],
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
