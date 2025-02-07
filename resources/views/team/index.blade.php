@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Team Members</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Role</th>
                <th>Profile Picture</th> <!-- Add a column for the profile picture -->
            </tr>
        </thead>
        <tbody>
            @foreach($teamMembers as $member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->role }}</td>
                    <td>
                        @if($member->profile_picture)
                            <img src="{{ $member->profile_picture }}" alt="Page Media" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <span>No Profile Picture</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
