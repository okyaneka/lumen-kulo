<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Filestore extends Model
{
    protected $table = 'filestores';

    protected $fillable = ['path'];

    //
    public static function store(UploadedFile $file)
    {
        $filename = $file->getClientOriginalName();
        $path = 'files' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . date('Gis');
        $file->move($path, $filename);

        return self::create(['path' => $path . DIRECTORY_SEPARATOR . $filename]);
    }
}
