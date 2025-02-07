@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Team Members</h1>

    <!-- Button to Add New Team Member -->
    <a href="{{ route('admin.team.create') }}" class="btn btn-primary mb-3">Add New Member</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Role</th>
                <th>Bio</th>
                <th>Profile Picture</th> <!-- Add a column for the profile picture -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teamMembers as $member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->role }}</td>
                    <td>{{ $member->bio }}</td>
                    <td>
                        @if($member->profile_picture)
                            <img src="{{ asset('storage/' . $member->profile_picture) }}" alt="Profile Picture" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <span>No Profile Picture</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.team.edit', $member->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.team.destroy', $member->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
