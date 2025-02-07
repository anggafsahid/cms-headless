@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Public Media</h1>
        <div class="row">
            @foreach ($media as $item)
                <div class="col-md-4 mb-4">
                    @if (in_array(substr($item->file_type, 0, 5), ['image', 'video']))
                        <div class="card">
                            @if (strpos($item->file_type, 'image') !== false)
                                <img src="{{ $item->file_path }}" class="card-img-top" alt="{{ $item->file_name }}">
                            @elseif (strpos($item->file_type, 'video') !== false)
                                <video controls class="card-img-top">
                                    <source src="{{ $item->file_path }}" type="{{ $item->file_type }}">
                                </video>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->file_name }}</h5>
                                <p class="card-text">Type: {{ $item->file_type }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
