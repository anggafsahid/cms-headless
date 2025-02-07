@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Add New Team Member</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <input type="text" name="role" id="role" class="form-control" value="{{ old('role') }}" required>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Bio:</label>
            <textarea name="bio" id="bio" class="form-control" rows="4">{{ old('bio') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="profile_picture" class="form-label">Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.team.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
