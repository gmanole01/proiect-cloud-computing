<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;

class AuthController extends Controller {

    public function login_view() {
        return view('login');
    }

    public function login_submit(Request $request) {
        $data = $request->only(['email_address', 'password']);
        if(count($data) != 2) {
            return redirect()->route('login')->withErrors(['msg' => 'Invalid request!']);
        }

        $ok = auth()->attempt([
            'email_address' => $data['email_address'],
            'password' => $data['password']
        ]);
        if(!$ok) {
            return redirect()->route('login')->withErrors(['msg' => 'Username or password invalid!']);
        }

        return redirect('/');
    }

    public function register_view() {
        return view('register');
    }

    public function register_submit(Request $request) {
        $validator = Validator::make($request->all(), [
            'email_address' => 'required|email',
            'password' => 'required|min:8|max:24',
            'confirm_password' => 'required'
        ]);

        if($validator->fails()) {
            return redirect()->route('register')->withErrors(['msg' => $validator->messages()->first()]);
        }

        $data = $validator->validated();

        if($data['password'] != $data['confirm_password']) {
            return redirect()
                ->route('register')
                ->withErrors(['msg' => 'Passwords must match!']);
        }

        $existing = User::where('email_address', $data['email_address'])->exists();
        if($existing) {
            return redirect()->route('register')->withErrors(['msg' => 'This email address is already in use!']);
        }

        $users = User::all();
        $id = 0;
        foreach($users as $user) {
            if($user->id > $id) {
                $id = $user->id;
            }
        }
        $id = $id + 1;

        User::create([
            'id' => $id,
            'email_address' => $data['email_address'],
            'password' => Hash::make($data['password'])
        ]);

        // Send the mail.
        try {
            $process = new Process([
                'node', base_path('emailer/send.js'), $data['email_address'],
                'Cont creat!',
                'Contul tau a fost creat cu adresa ' . $data['email_address'] . '!'
            ]);
            $process->run();
        } catch(\Exception $e) {}

        auth()->attempt([
            'email_address' => $data['email_address'],
            'password' => $data['password']
        ]);

        return redirect()->route('register');
    }

}
