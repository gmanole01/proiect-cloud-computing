@extends('base')

@section('main')
    <div id="root">
        <h2 class="title">Register</h2>
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
            <div class="form-input">
                <label for="confirmPassword">Confirm Password</label>
                <input
                    id="confirmPassword"
                    name="confirm_password"
                    type="password"
                />
            </div>
            <button type="submit" class="submit">Submit</button>
            <div class="error">{{$errors->first() ?: ''}}</div>
        </form>
        <a href="/login">Already have an account? Click here to login</a>
    </div>
@endsection
