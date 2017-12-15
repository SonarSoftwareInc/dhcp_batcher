<?php

namespace App\Http\Requests;

use App\Exceptions\FormattingException;
use App\Rules\MacAddress;
use Illuminate\Foundation\Http\FormRequest;

class DhcpReceiptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'leased_mac_address' => ['required', 'string', new MacAddress()],
            'ip_address' => 'required|ip',
            'remote_id' => ['string', new MacAddress(), 'nullable'],
            'expired' => ['required','boolean'],
        ];
    }
}
