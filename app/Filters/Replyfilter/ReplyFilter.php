<?php

namespace App\Filters\Replyfilter;

//use SpamFilter;
//use RepeatCharacterFilter;

class ReplyFilter
{
    protected $available_filters = [
            'App\Filters\Replyfilter\SpamFilter',
            'App\Filters\Replyfilter\RepeatCharacterFilter'
        ];

    //protected function detect($text);

    public function applyFilters($text)
    {
        //echo('from reply filter');
        //$detected = false;
        foreach($this->available_filters as $filter)
        {
            
            (new $filter)->detect($text);
        }
    }
}