<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = []; //disabling mass assignment

    public function profileImage()
    {
        //$imagePath = ($this->image) ? '/storage/' . $this->image : '/img/blank user.svg';
        $imagePath = ($this->image) ? $this->image : '/img/blank user.svg'; //switched from local storage to s3

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
