@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $page->title }}</h1>
        <div class="content">
            {!! $page->content !!}
        </div>

        @if ($page->bannerMedia)
            <div class="media">
                <img src="{{ asset('storage/' . $page->bannerMedia) }}" alt="Page Media" class="img-fluid">
            </div>
        @endif
    </div>
@endsection
