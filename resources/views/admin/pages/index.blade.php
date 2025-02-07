@extends('admin.layouts.app') <!-- Adjust the layout as needed -->

@section('content')
    <h1>Pages</h1>
    
    <!-- Display success message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Add Page Button -->
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary mb-3">Create Page</a>

    <!-- Pages Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Media</th>
                <th>Title</th>
                <th>Status</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
                <tr>
                    <td>
                        @if($page->bannerMedia)
                            <img src="{{ asset('storage/' . $page->bannerMedia) }}" alt="Banner Media" class="img-fluid" style="width: 100px;"/>
                        @else
                            No media
                        @endif
                    </td>
                    <td>{{ $page->title }}</td>
                    <td>{{ ucfirst($page->status) }}</td>
                    <th>{{ $page->slug }}</th>
                    <td>
                        <!-- View Button -->
                        <a href="{{ route('admin.pages.show', $page->slug) }}" class="btn btn-info">View</a>
                        <!-- Edit Button -->
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this page?')">Delete</button>
                        </form>
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
