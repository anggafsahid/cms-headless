<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    /**
     * Get all media.
     */
    public function index()
    {
        try {
            $media = Media::all();

            if ($media->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No MEdia available',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Media retrieved successfully',
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
                'media' => 'required|file|mimes:jpg,jpeg,png,gif,mp4|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle file upload
            $file = $request->file('media');
            $filePath = $file->store('media', 'public'); // Store file in 'public/media' directory

            // Store media info in the database
            $media = Media::create([
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
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
     * Show the media file.
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

            // Get the file URL
            $url = Storage::url($media->file_path);
            //$url = url(Storage::url($media->file_path));
            //$url = asset('storage/' . $media->file_path);

            return response()->json([
                'success' => true,
                'data' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

     /**
     * Remove the specified page.
     */
    public function destroy($id)
    {
        try {
            $media = Media::find($id)->first();
            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }
            // Delete the file from storage
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }

            // Delete Media From Database
            if (!$media) {
                return response()->json(['message' => 'media not found'], 404);
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
}
