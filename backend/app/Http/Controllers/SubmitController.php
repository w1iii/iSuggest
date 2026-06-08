<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission; 

class SubmitController extends Controller
{
    // 2. Create a function to handle the saving process
   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category' => 'required|string',
    ]);

    $submission = Submission::create([
        'title' => $request->title,
        'description' => $request->description,
        'category' => $request->category,
        'user_id' => $request->user()->id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Submission saved successfully.',
        'data' => $submission
    ]);
}
}