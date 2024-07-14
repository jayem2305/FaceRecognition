<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FaceRecognitionController extends Controller
{
    public function scanFace(Request $request)
    {
        $request->validate([
            'image' => 'required|image'
        ]);

        $image = $request->file('image');
        $path = $image->store('images', 'public'); // Store in 'public/images'

        // Construct the command to execute the Python script
        $pythonScriptPath = base_path('app/python_scripts/script.py'); // Adjust path as necessary
        $command = "python {$pythonScriptPath} " .  storage_path("app/public/{$path}");

        // Execute the Python script
        $output = shell_exec($command);

        // Log command for debugging
        Log::info("Python script command: {$command}");
        Log::info("Python script output: " . print_r($output, true)); // Add this line for debugging

        if ($output !== null) {
            // Process $output as needed
            $result = json_decode($output, true);
            if ($result && $result['status'] === 'success') {
                // Handle success case
                return response()->json([
                    'status' => 'success',
                    'message' => 'Face recognition successful.',
                    'data' => $result['data']
                ]);
            } else {
                // Handle error case
                $errorMessage = isset($result['message']) ? $result['message'] : 'Unknown error';
                return response()->json([
                    'status' => 'error',
                    'message' => 'Face recognition failed: ' . $errorMessage
                ]);
            }
        } else {
            // Handle shell_exec failure
            $error = error_get_last();
            Log::error("Error executing Python script: " . print_r($error, true));
            return response()->json([
                'status' => 'error',
                'message' => 'Error executing Python script.'.$command
            ]);
        }
    }
}
