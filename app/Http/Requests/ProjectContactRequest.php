<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'user_id'    => 'nullable|exists:users,id',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'message' => 'required|string|max:10000',
            // 'phone_number' => 'phone:INTERNATIONAL,BD',
            'phone_number' => 'nullable|string|max:20',

        ];
    }
}
