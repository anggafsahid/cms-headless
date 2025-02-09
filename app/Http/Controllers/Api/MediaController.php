<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MediaController extends Controller
{
    /**
     * Get all media.
     */
    public function index()
    {
        try {
            $media = Media::all();

            return response()->json([
                'success' => true,
                'message' => $media->isEmpty() ? 'No media available' : 'Media retrieved successfully',
                'total_data' => $media->count(),
                'data' => $media
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    /**
     * Store a newly uploaded media.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_path' => 'required|file|mimes:jpg,jpeg,png,gif,mp4|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle file upload to Cloudinary
            $uploadedFile = Cloudinary::upload($request->file('file_path')->getRealPath(), [
                'folder' => 'mediaFiles' // Define Cloudinary folder
            ]);

            $filePath = $uploadedFile->getSecurePath(); // Cloudinary URL
            $publicId = $uploadedFile->getPublicId(); // Public ID for later deletion

            // Store media info in the database
            $media = Media::create([
                'file_path'  => $filePath,
                'public_id'  => $publicId, // Save public_id for deletion
                'file_name'  => $request->file('media')->getClientOriginalName(),
                'file_size'  => $request->file('media')->getSize(),
                'file_type'  => $request->file('media')->getMimeType(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Media uploaded successfully',
                'data' => $media
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    /**
     * Show a specific media file.
     */
    public function show($id)
    {
        try {
            $media = Media::find($id);

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $media
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    /**
     * Remove the specified media.
     */
    public function destroy($id)
    {
        try {
            $media = Media::find($id);

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }

            // Delete the media from Cloudinary
            if ($media->public_id) {
                Cloudinary::destroy($media->public_id);
            }

            // Delete media from database
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
}
