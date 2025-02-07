<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class PageController extends Controller
{
    /**
     * Get all published pages.
     */
    public function index()
    {
        try {
            $pages = Page::all();
            if ($pages->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pages available',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pages retrieved successfully',
                'data' => $pages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }



    /**
     * Get a single page by slug.
     */
    public function show($slug)
    {
        try{
            $page = Page::where('slug', $slug)->first();

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Page retrieved successfully.',
                'data' => $page
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function showById($id)
    {
        try {
            $page = Page::find($id);

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Page retrieved successfully',
                'data' => $page
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }



    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:pages,slug',
                'content' => 'required|min:10',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|in:draft,published',
                'published_at' => 'nullable|date',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'author_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare data
            $data = $request->all();
            $data['slug'] = $request->filled('slug') ? $request->slug : $this->generateUniqueSlug($request->title);

            // Handle media upload to Cloudinary
            if ($request->hasFile('media')) {
                $uploadedFile = (new UploadApi())->upload($request->file('media')->getRealPath(), [
                    'folder' => 'pagesBannerMedia',
                ]);
                $data['media'] = $uploadedFile['secure_url'];
            }

            // Create the page
            $page = Page::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Page created successfully.',
                'data' => $page
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }



    /**
     * Update the specified page.
     */
    public function update(Request $request, $slug)
    {
        try {
            // Find the page by its slug
            $page = Page::where('slug', $slug)->first();

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
                'content' => 'nullable|string|min:10',
                'media' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
                'status' => 'sometimes|required|in:draft,published',
                'published_at' => 'nullable|date',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
                'author_id' => 'sometimes|required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update slug if provided, else regenerate from title
            $slug = $request->filled('slug') ? $request->input('slug') : $this->generateUniqueSlug($request->input('title', $page->title));

            // // Handle media upload if a new file is provided
            // $mediaPath = $page->media;
            // if ($request->hasFile('media')) {
            //     // Delete old media if exists
            //     if ($mediaPath) {
            //         Storage::disk('public')->delete($mediaPath);
            //     }
            //     // Store the new media
            //     $mediaPath = $request->file('media')->store('pagesBannerMedia', 'public');
            // }

            // Handle media upload if a new file is provided
            $mediaPath = $page->media;
            if ($request->hasFile('media')) {
                // Delete old media from Cloudinary if exists
                if ($mediaPath) {
                    $cloudinary = new Cloudinary();
                    $publicId = basename(parse_url($mediaPath, PHP_URL_PATH), '.' . pathinfo($mediaPath, PATHINFO_EXTENSION));
                    $cloudinary->uploadApi()->destroy($publicId); // Remove old image from Cloudinary
                }

                // Upload new media to Cloudinary
                $uploadedFile = $request->file('media');
                $cloudinary = new Cloudinary();
                $upload = $cloudinary->uploadApi()->upload($uploadedFile->getRealPath(), [
                    'folder' => 'pagesBannerMedia', // Define the folder in Cloudinary
                ]);
                $mediaPath = $upload['secure_url']; // Store the Cloudinary URL
            }

            // Update the page with only the provided fields
            $page->update([
                'title' => $request->input('title', $page->title),
                'slug' => $slug,
                'content' => $request->input('content', $page->content),
                'media' => $mediaPath, 
                'status' => $request->input('status', $page->status),
                'published_at' => $request->input('published_at', $page->published_at),
                'meta_title' => $request->input('meta_title', $page->meta_title),
                'meta_description' => $request->input('meta_description', $page->meta_description),
                'meta_keywords' => $request->input('meta_keywords', $page->meta_keywords),
                'author_id' => $request->input('author_id', $page->author_id),
            ]);

            // Return the updated page
            return response()->json([
                'success' => true,
                'message' => 'Page updated successfully',
                'data' => $page
            ], 200);
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
    public function destroy($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        // // Delete the file from storage
        // if ($page->media) {
        //     Storage::disk('public')->delete($page->media);
        // }
        
        // Delete the file from Cloudinary if it exists
        if ($page->media) {
            $cloudinary = new Cloudinary();
            $publicId = basename(parse_url($page->media, PHP_URL_PATH), '.' . pathinfo($page->media, PATHINFO_EXTENSION));
            $cloudinary->uploadApi()->destroy($publicId); // Remove image from Cloudinary
        }
       
        // Delete Pages data from database
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully'
        ]);
    }

    // Helper function to generate a unique slug
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = Page::where('slug', 'like', $slug.'%')->count();
        return $count ? $slug.'-'.$count : $slug;
    }

}
