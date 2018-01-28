<?php


namespace App\Rules;
use App\Filters\Threadfilter\ThreadFilter as ThreadSpamFilter;
use App\Filters\Replyfilter\ReplyFilter;
use \Exception;

class SpamValidator
{

    public function passes($attribute, $value, $parameters)
    {
        //dd($parameters);
        if($parameters[0] === 'thread')
        {
            try {
                    $contain_spam = app(ThreadSpamFilter::class)->applyFilters(
                        [
                            $attribute => $value,
                        ]);
                    
                    return !$contain_spam;      //if no spam, this variable will be equal to false, so we are returing true
            }catch(Exception $e){
            
                return false;
            }
        }
        else if($parameters[0] === 'reply')
        {
            //try{
                    $contain_spam = app(ReplyFilter::class)->applyFilters($value);
                    return !$contain_spam;      //if no spam, this variable will be equal to false, so we are returing true
            // }catch(Exception $e){

            //     return false;
            // }   
        }
    }
}