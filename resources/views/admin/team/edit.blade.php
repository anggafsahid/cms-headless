@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Team Member</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.team.update', $team->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $team->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <input type="text" name="role" id="role" class="form-control" value="{{ old('role', $team->role) }}" required>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Bio:</label>
            <textarea name="bio" id="bio" class="form-control" rows="4">{{ old('bio', $team->bio) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="profile_picture" class="form-label">Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control">
            @if ($team->profile_picture)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $team->profile_picture) }}" alt="Profile Picture" width="100">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.team.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
