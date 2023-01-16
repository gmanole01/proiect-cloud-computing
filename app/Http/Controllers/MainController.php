<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use svay\FaceDetector;

class MainController extends Controller {

    public function home() {
        return view('home', [
            'images' => Image::query()->where('user_id', (int) auth()->user()->id)
                ->get()
                ->sort(function($a, $b) {
                    return $b->id <=> $a->id;
                }),
        ]);
    }

    public function add_image() {
        return view('add_image');
    }

    public function add_image_submit(Request $request) {
        $file = $request->file('file');
        if(!$file) {
            return redirect()->route('add_image')
                ->withErrors(['msg' => 'No file!']);
        }

        $filename = $file->getClientOriginalName();

        if(!Str::endsWith($filename, '.jpg') && !Str::endsWith($filename, '.jpeg')) {
            return redirect()->route('add_image')
                ->withErrors(['msg' => 'Invalid file!']);
        }

        $temp = tempnam(sys_get_temp_dir(), '');

        $path = $file->getPath() . '/' . $file->getFilename();

        try {
            $detector = new FaceDetector();
            $detector->faceDetect($path);
            $detector->cropFaceToJpeg($temp);
        } catch(\Exception $e) {
            return redirect()->route('add_image')
                ->withErrors(['msg' => 'Error: ' . $e->getMessage()]);
        }

        $user = auth()->user();
        $key = md5($user->email_address);

        $rand = Str::random();
        $azureOriginal = $key . '_' . $rand . '.jpg';
        $azureFace = $key . '_' . $rand . '_face.jpg';

        Storage::disk('azure')->put($azureOriginal, file_get_contents($path));
        Storage::disk('azure')->put($azureFace, file_get_contents($temp));

        $images = Image::all();
        $id = 0;
        foreach($images as $image) {
            if($image->id > $id) {
                $id = $image->id;
            }
        }
        $id = $id + 1;

        Image::create([
            'id' => $id,
            'user_id' => $user->id,
            'url' => Storage::disk('azure')->url($azureOriginal),
            'face_url' => Storage::disk('azure')->url($azureFace)
        ]);

        return redirect()->route('home');

    }

    public function get_image(Request $request, $imageId) {
        $image = Image::query()
            ->where('id', (int)$imageId)
            ->where('user_id', (int) auth()->user()->id)
            ->first();
        return view('image', [
            'image' => $image,
        ]);
    }
}
