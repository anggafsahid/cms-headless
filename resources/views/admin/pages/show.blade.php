@extends('admin.layouts.app')

@section('content')

    <h1>{{ $page->title }}</h1>

    <p>{{ $page->content }}</p>

    @if ($page->bannerMedia)
        @if (in_array(pathinfo($page->bannerMedia, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
            <!-- Display image -->
            <img src="{{ asset('storage/' . $page->bannerMedia) }}" alt="Banner Media" class="img-fluid">
        @elseif (in_array(pathinfo($page->media, PATHINFO_EXTENSION), ['mp4', 'avi', 'mov']))
            <!-- Display video -->
            <video class="img-fluid" controls>
                <source src="{{ url('storage/pages/' . basename($page->banner_media)) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @endif
    @endif

    <!-- SEO Fields -->
    <p>{{ $page->meta_title }}</p>

    <p>{{ $page->meta_description }}</p>

    <p>{{ $page->meta_keywords }}</p>
   
@endsection
