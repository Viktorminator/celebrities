<?php

namespace App\Http\Requests;

use App\Models\Card;
use App\Models\StyleImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStyleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $style = Card::where('user_id', auth()->id())
            ->find($this->route('id'));

        return $style !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $cardId = $this->route('id');

        return [
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|array|max:20',
            'tags.*' => 'string|max:50',
            'links' => 'nullable|array|max:50',
            'links.*.id' => [
                'nullable',
                'integer',
                Rule::exists('product_links', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'links.*.title' => 'required_with:links.*.url|string|max:255',
            'links.*.url' => 'required_with:links.*.title|url|max:500',
            'links.*.platform' => 'nullable|string|max:255',
            'links.*.price' => 'nullable|string|max:255',
            'keep_images' => 'nullable|array|max:5',
            'keep_images.*' => [
                'integer',
                Rule::exists('style_images', 'id')->where(function ($query) use ($cardId) {
                    return $query->where('card_id', $cardId);
                }),
            ],
            // Don't validate max here - we'll validate total count in withValidator
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $style = Card::where('user_id', auth()->id())
                ->with('images')
                ->find($this->route('id'));

            if (!$style) {
                return;
            }

            // Get and validate keep_images
            $keepImageIds = collect($this->input('keep_images', []))
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            // Get new images
            $newImages = $this->file('new_images', []);

            // Validate that keep_images actually belong to this style
            $validKeepImageIds = $keepImageIds->filter(function ($id) use ($style) {
                return $style->images->contains('id', $id);
            })->values();

            // Calculate final image count
            $finalImageCount = $validKeepImageIds->count() + count($newImages);

            // Validate minimum images
            if ($finalImageCount === 0) {
                $validator->errors()->add('images', 'At least one image is required. Please keep an existing image or upload a new one.');
            }

            // Validate maximum images
            if ($finalImageCount > 5) {
                $validator->errors()->add('images', 'Maximum 5 images allowed. You have ' . $validKeepImageIds->count() . ' existing and ' . count($newImages) . ' new images.');
            }

            // Validate that each new image is actually an uploaded file
            if (!empty($newImages)) {
                foreach ($newImages as $index => $image) {
                    if (!$image || !$image->isValid()) {
                        $validator->errors()->add("new_images.{$index}", 'Invalid image file.');
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'new_images.*.image' => 'Each file must be an image.',
            'new_images.*.mimes' => 'Images must be jpeg, png, jpg, or gif format.',
            'new_images.*.max' => 'Each image must not exceed 10MB.',
            'keep_images.*.exists' => 'Selected image does not exist or does not belong to this style.',
            'links.*.title.required_with' => 'Link title is required when URL is provided.',
            'links.*.url.required_with' => 'Link URL is required when title is provided.',
            'links.*.url.url' => 'Link URL must be a valid URL.',
        ];
    }
}
