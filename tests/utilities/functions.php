<?php

function create($className, $attributes = [], $noOfItems = null)
{
    return factory($className, $noOfItems)->create($attributes);
}


function make($className, $attributes = [], $noOfItems = null)
{
    return factory($className, $noOfItems)->make($attributes);
}