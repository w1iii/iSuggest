<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class UpdateProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('Profile Update Request', ['has_file' => $request->hasFile('image')]);
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'field' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('profile_images', 'public');
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ], 200);
    }
}
