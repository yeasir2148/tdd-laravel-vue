<?php

namespace App\Filters\Replyfilter;
use \Exception;


class RepeatCharacterFilter
{
    function detect($text)
    {
        $pattern = '/(.)\\1{3,}/';
        $found = 0;

        $found = preg_match($pattern,$text);

        //dd($found);
        if($found > 0)
            throw new Exception('Repeated sequence of characters');
            //return true;
        else
            return false;
    }
}