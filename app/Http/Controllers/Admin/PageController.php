<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    // Display a list of pages
    public function index()
    {
        $pages = Page::all();
        return view('admin.pages.index', compact('pages'));
    }

    // Show the form to create a new page
    public function create()
    {
        return view('admin.pages.create');
    }

    // Store a new page
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'bannerMedia' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,avi|max:10240', // validate media
            'status' => 'nullable|in:draft,published',
        ]);

        // Handle media upload
        $mediaPath = null;
        if ($request->hasFile('bannerMedia')) {
            $mediaPath = $request->file('bannerMedia')->store('pagesBannerMedia', 'public'); // Store in 'public/media' directory
        }

        Page::create([
            'title' => $request->title,
            'slug' => $this->generateUniqueSlug($request->title),
            'content' => $request->content,
            'bannerMedia' => $mediaPath, // Store media path
            'author_id' => auth()->id(),
            'status' => $request->status ?? 'draft',
            'published_at' => $request->status === 'published' ? now() : null,
            'meta_title' => $request->meta_title, // Save meta title
            'meta_description' => $request->meta_description, // Save meta description
            'meta_keywords' => $request->meta_keywords, // Save meta keywords
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    // Update the page
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'bannerMedia' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,avi|max:10240', // validate media
            'status' => 'nullable|in:draft,published',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Handle media upload
        $mediaPath = $page->bannerMedia;
        if ($request->hasFile('bannerMedia')) {
            // Delete old media if it exists
            if ($mediaPath) {
                Storage::disk('public')->delete($mediaPath);
            }
            // Store new media
            $mediaPath = $request->file('bannerMedia')->store('pagesBannerMedia', 'public');
        }

        // Update the page with the validated data
        $page->update([
            'title' => $request->title,
            'slug' => $this->generateUniqueSlug($request->title), // Generate new slug if title changes
            'content' => $request->content,
            'bannerMedia' => $mediaPath, // Store media path
            'status' => $request->status ?? 'draft',
            'published_at' => $request->status === 'published' ? now() : null,
            'meta_title' => $request->meta_title, // Save meta title
            'meta_description' => $request->meta_description, // Save meta description
            'meta_keywords' => $request->meta_keywords, // Save meta keywords
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    // Show the form to edit a page
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Remove the specified page.
     */
    public function destroy($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        // Delete the file from storage
        if ($page->bannerMedia) {
            Storage::disk('public')->delete($page->bannerMedia);
        }
       
        // Delete Pages data from database
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }


    // In PageController.php
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();
        return view('admin.pages.show', compact('page'));
    }

    // Public Index (for listing pages publicly)
    public function publicIndex()
    {
        $pages = Page::where('status', 'published')->get();  // Only show published pages
        return view('pages.index', compact('pages'));
    }
    
    // Add the 'publicShow' method for public access to pages
    public function publicShow($slug)
    {
        // Find the page by slug, ensuring it is published
        $page = Page::where('slug', $slug)->where('status', 'published')->first();

        // If the page is not found, redirect or show a 404 page
        if (!$page) {
            abort(404);
        }

        // Return the view with the page data
        return view('pages.show', compact('page'));  // Public Page Show View
    }

    // Helper function to generate a unique slug
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = Page::where('slug', 'like', $slug.'%')->count();
        return $count ? $slug.'-'.$count : $slug;
    }
    
}
