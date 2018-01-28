<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordsActivity;

class Favorite extends Model
{
    //

    use RecordsActivity;

    protected $fillable = ['user_id'];

    public function favorited()
    {
        return $this->morphTo();
    }

    public function activities()
    {
        return $this->morphMany('App\Activity','object');
    }
}
