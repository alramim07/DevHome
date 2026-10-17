<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'custom_cv'     => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'designation'   => ['nullable', 'string', 'max:255'],
            'facebook_url'  => ['nullable', 'url'],
            'linkedin_url'  => ['nullable', 'url'],
            'github_url'    => ['nullable', 'url'],
            'experience'    => ['nullable', 'string'],
            'languages'     => ['nullable', 'string'],
            'password'      => ['nullable', 'min:8'],
        ];
    }
}
