<?php

namespace App\Filters\Replyfilter;

use \Exception;

class SpamFilter
{
    protected $banned_words = ['sex','fuck'];
    
    public function detect($text)
    {
        $found = 0;
        $total = 0;
        //echo("text: ". $text);
        foreach($this->banned_words as $banned_word)
        {
            $found = preg_match("/$banned_word/i",$text);
            $total += $found;
        }


        //dd('found: '.$found);
        //dd($total);
        if($total > 0)
            throw new Exception('spam detected in reply');
        else
            return false;
    }

}