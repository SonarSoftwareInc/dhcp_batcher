<?php

namespace App\Rules;

use App\Exceptions\FormattingException;
use App\Services\Formatter;
use Illuminate\Contracts\Validation\Rule;

class MacAddress implements Rule
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
        $formatter = new Formatter();
        try {
            $formatter->formatMac($value);
        }
        catch (FormattingException $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid MAC address.';
    }
}
