<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission; 

class SubmitController extends Controller
{
    // 2. Create a function to handle the saving process
    public function store(Request $request)
    {
        // Save the incoming data to SQLite database
        $submission = Submission::create([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'user_id'     => auth()->id(),
        ]);

        // Return the JSON response e
        return response()->json([
            'success' => true,
            'message' => 'Submission saved successfully.',
            'data'    => $submission,
            'status'  => 200
        ]);
    }
}