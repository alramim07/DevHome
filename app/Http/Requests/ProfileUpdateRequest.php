<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('slug') && filled($this->slug)) {
            $slug = Str::slug($this->slug);
            $this->merge([
                'slug' => $slug !== '' ? $slug : null,
            ]);
        }
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
            'slug'          => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:users,slug,' . $this->user()->id],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'custom_cv'     => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'biography'     => ['nullable', 'string', 'max:1000'],
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
