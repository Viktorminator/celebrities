<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStyleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'links' => 'nullable|array',
            'links.*.title' => 'required_with:links.*.url|string|max:255',
            'links.*.url' => 'required_with:links.*.title|url|max:500',
            'links.*.platform' => 'nullable|string|max:255',
            'links.*.price' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Please select at least one image.',
            'photos.min' => 'Please select at least one image.',
            'photos.max' => 'You can upload a maximum of 5 images.',
            'photos.*.required' => 'Each image is required.',
            'photos.*.image' => 'All files must be images.',
            'photos.*.mimes' => 'All images must be of type: jpeg, png, jpg, gif.',
            'photos.*.max' => 'Each image may not be greater than 10MB.',
            'links.*.title.required_with' => 'Link title is required when URL is provided.',
            'links.*.url.required_with' => 'Link URL is required when title is provided.',
            'links.*.url.url' => 'Please provide valid URLs for product links.',
        ];
    }

}
