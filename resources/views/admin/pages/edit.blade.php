@extends('admin.layouts.app')

@section('content')
    <h1>Edit Page</h1>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')  <!-- Required for update -->

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $page->title) }}" required>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content', $page->content) }}</textarea>
        </div>

        <div class="form-group">
            <label for="media">Upload Media</label>
            <input type="file" name="media" id="media" class="form-control">
            @if($page->media)
                <p>Current Media: <a href="{{ asset('storage/' . $page->media) }}" target="_blank">View</a></p>
            @endif
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $page->slug) }}" required>
        </div>

        <div class="form-group">
            <label for="author_id">Author</label>
            <input type="text" name="author_id" id="author_id" class="form-control" value="{{ old('author_id', $page->author_id) }}" required>
        </div>

        <div class="form-group">
            <label for="published_at">Published At</label>
            <input type="text" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', $page->published_at) }}" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="draft" {{ old('status', $page->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $page->status) == 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>

        <!-- SEO Fields -->
        <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
        </div>

        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea name="meta_description" id="meta_description" class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="meta_keywords">Meta Keywords</label>
            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}">
        </div>

        <button type="submit" class="btn btn-warning">Update Page</button>
    </form>
@endsection
