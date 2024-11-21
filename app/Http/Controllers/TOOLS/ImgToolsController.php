<?php

namespace App\Http\Controllers\TOOLS;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImgToolsController
{
    public function addImage($folder, $file)
    {
        // set name image and get extension
        // random string name
        $name = Str::random(10) . '.' . $file->getClientOriginalExtension();;
        // destination path
        return Storage::putFileAs($folder, $file, $name);
    }
}
