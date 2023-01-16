@extends('page')

@section('page')
    <table>
        <tr>
            <th>Id</th>
            <th>Image</th>
        </tr>
        @foreach($images as $image)
            <tr>
                <td><a href="/image/{{$image['id']}}">{{$image['id']}}</a></td>
                <td><img src="{{$image['url']}}" alt="img" /></td>
            </tr>
        @endforeach
    </table>
@endsection
