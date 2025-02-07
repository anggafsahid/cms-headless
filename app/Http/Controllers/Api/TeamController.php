<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class TeamController extends Controller
{
    public function index()
    {
        try {
            // Get all team members (you can paginate if needed)
            $teamMembers = Team::all();

            return response()->json([
                'success' => true,
                'data' => $teamMembers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:5120',  // Example validation for profile picture
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors()
            ], 422);
        }

        $team = new Team();
        $team->name = $request->name;
        $team->role = $request->role;
        $team->bio = $request->bio;

       // Handle profile picture upload if present
        if ($request->hasFile('profile_picture')) {
            // Initialize Cloudinary
            $cloudinary = new Cloudinary();
            $uploadedFile = $cloudinary->upload($request->file('profile_picture')->getRealPath());

            // Save the URL returned by Cloudinary
            $team->profile_picture = $uploadedFile['secure_url'];
        }

        $team->save();

        return response()->json($team, 201);
    }

    public function show($id)
    {
        try {
            $team = Team::where('id', $id)->first();
            if (!$team) {
                // Return an error if the team member doesn't exist
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $team
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $team = Team::where('id', $id)->first();

            if (!$team) {
                // Return an error if the team member doesn't exist
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'bio' => 'nullable|string',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120', // 5MB max
            ]);

            // Return validation errors if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()
                ], 422);
            }

            /// Handle file upload if present
            if ($request->hasFile('profile_picture')) {
                // Initialize Cloudinary
                $cloudinary = new Cloudinary();

                // Delete old profile picture from Cloudinary if it exists
                if ($team->profile_picture) {
                    $imagePublicId = pathinfo($team->profile_picture, PATHINFO_FILENAME);
                    $cloudinary->destroy($imagePublicId);
                }

                // Upload new profile picture to Cloudinary
                $uploadedFile = $cloudinary->upload($request->file('profile_picture')->getRealPath());
                $team->profile_picture = $uploadedFile['secure_url'];
            }

            // Update the team member details (excluding profile_picture)
            $team->update($request->except('profile_picture'));

            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Team member updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $team = Team::where('id', $id)->first();

            if (!$team) {
                // Return an error if the team member doesn't exist
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }

            // // Delete the file from storage
            // if ($team->profile_picture) {
            //     Storage::disk('public')->delete($team->profile_picture);
            // }

            // Delete the profile picture from Cloudinary if it exists
            // Initialize Cloudinary object
            $cloudinary = new Cloudinary();
            // Delete the profile picture from Cloudinary if it exists
            if ($team->profile_picture) {
                // Cloudinary's destroy method requires the public ID of the file
                $imagePublicId = pathinfo($team->profile_picture, PATHINFO_FILENAME); // Extract public ID from file path
                $cloudinary->destroy($imagePublicId); // Delete from Cloudinary
            }
        
            // Delete Pages data from database
            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team member deleted successfully.'
            ], 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
}
