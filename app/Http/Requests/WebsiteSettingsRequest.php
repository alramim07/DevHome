<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteSettingsRequest extends FormRequest
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
            'tagline' => 'nullable|string|min:20|max:755',
            'tagline_h' => 'nullable|string|min:25|max:630',
            'tagline_p' => 'nullable|string|min:140|max:300',
            'about_us_img1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'about_us_img2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'what_we_build' => 'nullable|string|min:45|max:650',
            'why_dev_home' => 'nullable|string|min:45|max:650',
            'how_we_work' => 'nullable|string|min:45|max:650',
            'footer_des' => 'nullable|string|min:45|max:650',
            'hero_svg1' => 'nullable|string',
            'hero_svg2' => 'nullable|string',
            'hero_svg3' => 'nullable|string',
            'hero_svg4' => 'nullable|string',
            'fav_icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
