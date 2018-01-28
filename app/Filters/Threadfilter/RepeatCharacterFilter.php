<?php

namespace App\Filters\Threadfilter;
use \Exception;


class RepeatCharacterFilter
{
    function detect($texts)
    {
        $pattern = '/(.)\\1{3,}/';
        $found = 0;

        foreach($texts as $thread_attribute => $value)
        {
            if($thread_attribute == 'body')
                
            $found = preg_match($pattern,$value);  
            if($found)
            {
                //dd($found);
                throw new Exception('Repeated sequence of characters in '. $thread_attribute);  
            }
        }
        
        return false;               //no spam is detected
    }
}