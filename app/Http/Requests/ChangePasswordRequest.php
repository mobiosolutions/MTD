<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password' => 'required|min:8|Different:new_password|max:15',
            'new_password' => 'required|min:8|Same:confirm_password|max:15',
            'confirm_password' => 'required|min:8|max:15'
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'old_password.required' => 'Old password is required!',
            'new_password.required' => 'New password is required!',
            'confirm_password.required' => 'Confirm Password is required!'
        ];
    }
}
