<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Session;
class Captcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        if(!Session::has('milkcaptcha'))return false;
        //var_dump(Session::get('milkcaptcha'),$value);die;
        return Session::get('milkcaptcha') == $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '验证码错误.';
    }
}
