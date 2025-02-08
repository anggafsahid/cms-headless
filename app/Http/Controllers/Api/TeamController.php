<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TeamController extends Controller
{
    public function index()
    {
        try {
            $teamMembers = Team::all();

            return response()->json([
                'success' => true,
                'total_data' => $teamMembers->count(),
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
            'profile_picture' => 'nullable|image|max:5120',
        ]);

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

        if ($request->hasFile('profile_picture')) {
            $uploadedFile = Cloudinary::upload($request->file('profile_picture')->getRealPath());
            $team->profile_picture = $uploadedFile->getSecurePath();
        }

        $team->save();

        return response()->json([
            'success' => true,
            'data' => $team
        ], 201);
    }

    public function show($id)
    {
        try {
            $team = Team::find($id);
            if (!$team) {
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
        try {
            $team = Team::find($id);

            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'bio' => 'nullable|string',
                'profile_picture' => 'nullable|image|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle profile picture update
            if ($request->hasFile('profile_picture')) {
                // Delete old image if exists
                if ($team->profile_picture) {
                    $publicId = pathinfo(parse_url($team->profile_picture, PHP_URL_PATH), PATHINFO_FILENAME);
                    Cloudinary::destroy($publicId);
                }

                // Upload new image
                $uploadedFile = Cloudinary::upload($request->file('profile_picture')->getRealPath());
                $team->profile_picture = $uploadedFile->getSecurePath();
            }

            // Update other fields
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
            $team = Team::find($id);

            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }

            // Delete profile picture from Cloudinary if exists
            if ($team->profile_picture) {
                $publicId = pathinfo(parse_url($team->profile_picture, PHP_URL_PATH), PATHINFO_FILENAME);
                Cloudinary::destroy($publicId);
            }

            // Delete the team member from database
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
