<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;
class Sms implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($mobile = '', $type = '')
    {
        //
        $this->cache_key = $mobile.$type;
        $this->mobile = $mobile;
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
        if(!$this->cache_key)return false;
        
        if (!Cache::has($this->cache_key))return false;
        //取出验证码
        $code = Cache::get($this->cache_key);
        Cache::forget($this->cache_key);
        
        return $code === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '手机验证码错误.';
    }
}
