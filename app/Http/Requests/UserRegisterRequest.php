<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
            'phone' => 'regex:/[0-9]{3} [0-9]{3} [0-9]{4}/|unique:users,phone',
            'email' => 'email|unique:users,email',
            'password' => 'required|string|min:6'
        ];
    }
}
