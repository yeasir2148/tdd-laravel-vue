<?php

namespace App\Filters\Threadfilter;

use \Exception;

class SpamFilter
{
    protected $banned_words = ['sex','fuck'];
    
    public function detect($texts)
    {
        $found = 0;
        $total = 0;
        //echo("text: ". $text);
        foreach($this->banned_words as $banned_word)
        {
            //dd('here');
            foreach($texts as $thread_attribute => $value)
            {

                $found = preg_match("/$banned_word/i",$value);
                //dd($found);
                if($found)
                {
                    //dd('here');
                    throw new Exception('spam detected in '. $thread_attribute);
                }
            }

            //$found = preg_match("/$banned_word/i",$text);
            // if($found)
            //     throw new Exception('spam detected in reply');
        }

        return false;
    }

}