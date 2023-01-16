<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller {

    public function all(Request $request) {
        return response([
            'error' => false,
            'images' => User::all(),
        ]);
    }

    public function single(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);

    }

    public function submit(Request $request) {
        return response('test');
    }

}
