<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // 
    protected $table = 'user';

    protected $fillable = ['firstname', 'lastname', 'dob', 'address', 'photo'];

    protected $hidden = ['photo'];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        return url($this->photo);
    }
}
