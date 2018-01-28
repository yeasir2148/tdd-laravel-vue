<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ThrottleException;
use App\Reply;

class CreateReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    
    protected function failedAuthorization()
    {
        throw new ThrottleException('You are posting too frequently. Take a break maite ;)');
    }

    public function authorize()
    {
        //dd(\Request::route()->getName());
        return auth()->user()->can('create',[Reply::class, \Request::route('thread')->id, 1]);
        //dd('in authorize method of create reply request');
        //parent::authorize('create', [Reply::class, route('thread'), 1])
        //return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'body'=>'required | max: 500 | spamFree:reply',
        ];
    }


}
