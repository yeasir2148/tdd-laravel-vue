<?php

namespace App\Filters;

abstract class Filter
{
    protected $allowed_filters = [];

    public function apply($query, $filters)
    {
        
        //var_dump($this->available_filters);
        foreach($filters as $key=>$value)
        {
            if(in_array($key,$this->available_filters))
            {
                $this->allowed_filters[$key] = $value;
            }
        }

        //dd($this->allowed_filters);
        foreach($this->allowed_filters as $filter_key=>$value)
        {

            if(method_exists($this, $filter_key))
            {
                $query = $this->$filter_key($query, $value);
            }
            
        }

        return $query;
    }
}