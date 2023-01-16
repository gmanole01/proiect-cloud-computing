@extends('page')

@section('page')
    <form method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" />
        <button type="submit">Upload</button>
    </form>
    <div class="error">{{ $errors->first() ?: '' }}</div>
@endsection
