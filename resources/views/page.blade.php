@extends('base')

@section('main')
    <div class="nav-bar">
        <div class="nav-bar-inner">
            <div>Hello, {{ auth()->user()->email_address }}</div>
            <a href="/">Home</a>
            <a href="{{ route('add_image') }}">Add Photo</a>
        </div>
        <a href="#" onClick={logout}>
            Log out
        </a>
    </div>
    @yield('page')
@endsection
