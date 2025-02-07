@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Welcome to the Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Pages</div>
                <div class="card-body">
                    <p>{{ $pageCount }} Pages</p>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-primary">Manage Pages</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Media</div>
                <div class="card-body">
                    <p>{{ $mediaCount }} Media Files</p>
                    <a href="{{ route('admin.media.index') }}" class="btn btn-primary">Manage Media</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Team Members</div>
                <div class="card-body">
                    <p>{{ $teamMemberCount }} Members</p>
                    <a href="{{ route('admin.team.index') }}" class="btn btn-primary">Manage Team</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
