<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        // Get all media from the database
        $media = Media::all();
        
        // Return the media index view with the media data
        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request)
    {
        // Validate the file upload
        $request->validate([
            'media' => 'required|mimes:jpeg,jpg,png,gif,mp4,mkv,avi|max:10240', // Accept only certain file types and size
        ]);

        // Get the uploaded file
        $media = $request->file('media');

        // Store the file in the 'media' folder inside the public directory
        $filePath = $media->store('media', 'public');

        // Create a new media record in the database
        $mediaRecord = new Media();
        $mediaRecord->file_name = $media->getClientOriginalName();
        $mediaRecord->file_path = $filePath;
        $mediaRecord->file_type = $media->getClientMimeType();
        $mediaRecord->save();

        // Redirect back to the media index page
        return redirect()->route('admin.media.index');
    }

    // Public Index (for listing pages publicly)
    public function publicIndex()
    {
        // Get all media from the database
        $media = Media::all();
        // Return the media index view with the media data
        return view('media.index', compact('media'));
    }

     /**
     * Remove the specified page.
     */
    public function destroy($id)
    {
        
        $media = Media::find($id)->first();

        if (!$media) {
            return response()->json(['message' => 'media not found'], 404);
        }

        // Delete the file from storage
        Storage::disk('public')->delete($media->file_path);

        // Delete the media record from the database
        $media->delete();

        return response()->json(['message' => 'Page deleted successfully']);
    }
}
