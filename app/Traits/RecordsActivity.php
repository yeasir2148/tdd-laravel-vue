<?php

namespace App\Traits;
use App\Activity;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {        
        if(auth()->guest())
            return;
        //dd(count(static::getEventsToBeRecorded()));
        foreach(static::getEventsToBeRecorded() as $event)
        {
            static::$event(function($model) use ($event)
            {
               $model->recordActivity($event);
               //dd('hi');
            });   
        }
    }

    protected static function getEventsToBeRecorded()
    {
        return ['created'];
    }

    protected function recordActivity($event)
    {
        // dd('hi from '.__CLASS__);
        
        $this->activities()->create([
            //'user_id'=>auth()->id(),
            'user_id'=>auth()->id(),
            'activity_type'=>$event,
        ]);
        

        /*Activity::create([
            'user_id'=>auth()->id(),
            'object_id'=> $this->id,
            'object_type'=>(new \ReflectionClass($this))->getShortName(),
            'activity_type'=>$event, 
           ]); 
        */
    }
}