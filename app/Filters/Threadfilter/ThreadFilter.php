<?php

namespace App\Filters\Threadfilter;

use App\Filters\Replyfilter\ReplyFilter;

class ThreadFilter extends ReplyFilter
{
  protected $available_filters = [
            'App\Filters\Threadfilter\SpamFilter',
            'App\Filters\Threadfilter\RepeatCharacterFilter'
        ];

    //protected function detect($text);

    public function applyFilters($texts = [])
    {
        //echo('from reply filter');
        //$detected = false;
        foreach($this->available_filters as $filter)
        {
            $instance = (new $filter);
            //dd($instance);
            $instance->detect($texts);
        }
    }

}