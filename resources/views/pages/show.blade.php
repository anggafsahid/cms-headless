@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $page->title }}</h1>
        <div class="content">
            {!! $page->content !!}
        </div>

        @if ($page->media)
            <div class="media">
                <img src="{{ asset('storage/' . $page->media) }}" alt="Page Media" class="img-fluid">
            </div>
        @endif
    </div>
@endsection
