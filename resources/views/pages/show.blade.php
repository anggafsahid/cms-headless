@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $page->title }}</h1>

        <!-- Display Author and Published At in small text below the title -->
        <p class="small text-muted">
            By {{ $page->author->name }} | Published on {{ \Carbon\Carbon::parse($page->published_at)->format('F j, Y') }}
        </p>

        <br>

        <div class="content">
            {!! $page->content !!}
        </div>

        @if ($page->media)
            <div class="media">
                <img src="{{ $page->media }}" alt="Page Media" class="img-fluid">
            </div>
        @endif
    </div>
@endsection
