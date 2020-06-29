<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = []; //disabling mass assignment

    public function profileImage()
    {
        $imagePath = ($this->image) ? '/storage/' . $this->image : '/img/blank user.svg';
        return $imagePath;
    }

    //one profile can have many followers
    public function followers()
    {
        return $this->belongsToMany(User::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
