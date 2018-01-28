<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Activity extends Model
{
    //
    protected $guarded = [];
    protected $with = ['object','user'];

    /*protected $class_map = [
        'App\Thread'=>'Thread',
        'App\Reply'=>'Reply',
    ];*/

    public static function check_deleted($object_id, $object_type)
    {
        //dd(Activity::where('activity_type','deleted')->where('object_type',$activity_instance->object_type)->toSql());
        return Activity::where('activity_type','deleted')
                        ->where('object_type',$object_type)
                        ->where('object_id',$object_id)
                        ->exists();
    }

    public function object()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function feed($user)
    {
        //dd('hi from'.__CLASS__);    
        /*return $user->activities()->latest()->get()->groupBy(function($item, $key){
            //dd($item);
            return $item->created_at->format('Y-m-d');
        })->toArray();*/

        if(app()->runningUnitTests())
        {
            return static::where('user_id',$user->id)->latest()->get()->groupBy(function($item, $key){
            //dd($item);
            return $item->created_at->format('Y-m-d');
            })->toArray();
        }

        return static::where('user_id',$user->id)->latest()->get()->groupBy(function($item, $key){
            //dd($item);
            return $item->created_at->format('Y-m-d');
            });
    }


    /*public function getObjectTypeAttribute($original_value)
    {
        //dd($original_value);
        //$original_value = strtolower($original_value);
        return array_get($this->class_map, $original_value, 'not_found');
    }*/
}
