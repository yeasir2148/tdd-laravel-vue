<?php

namespace App\Filters;

class ThreadFilter extends Filter
{

    // protected $allowed_filters = [];

    protected $available_filters = ['author', 'popularity','repliesCount'];

    // public function apply($query, $filters)
    // {
    //     //dd($filters);
    //     foreach($filters as $key=>$value)
    //     {
    //         if(in_array($key,$this->available_filters))
    //         {
    //             $this->allowed_filters[$key] = $value;
    //         }
    //     }

    //     //dd($this->allowed_filters);

    //     foreach($this->allowed_filters as $filter_key=>$value)
    //     {

    //         if(method_exists($this, $filter_key))
    //         {
    //             $query = $this->$filter_key($query, $value);
    //         }
            
    //     }

    //     return $query;
    // }

    protected function author($query, $value)
    {
        //var_dump($this->allowed_filters);
        return $query->where('user_id',$value);
    }

    protected function popularity($query, $value)
    {
        //dd('hi');
        $query->getQuery()->order = [];
        return $query->orderBy('replies_count','desc');
    }

    protected function repliesCount($query, $value)
    {
        //$query->getQuery()->groups = ['replies_count'];
       
        return $query->has('replies','=',$value);
       
       
    }
}