@extends('base')

@section('main')
    <div id="root">
        <h2 class="title">Login</h2>
        <form class="auth-form" method="post">
            @csrf
            <div class="form-input">
                <label for="email">Email</label>
                <input id="email" name="email_address" />
            </div>
            <div class="form-input">
                <label for="password">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                />
            </div>
            <button type="submit" class="submit">Submit</button>
            <div class="error">{{ $errors->first() ?: '' }}</div>

        </form>
        <a href="/register">Don't have an account? Click here to register</a>
    </div>
@endsection
