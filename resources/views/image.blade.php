@extends('page')

@section('page')
    <div>Original image</div>
    <img src="{{$image->url}}" alt="Original image"/>
    <div>Face</div>
    <img src="{{$image->face_url}}" alt="Face image"/>
@endsection
