@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>All Pages</h1>
        <div class="list-group">
            @foreach ($pages as $page)
                <a href="{{ route('page.show', $page->slug) }}" class="list-group-item list-group-item-action">
                    <h5>{{ $page->title }}</h5>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($page->content), 150) }}</p>
                </a>
            @endforeach
        </div>
    </div>
@endsection
