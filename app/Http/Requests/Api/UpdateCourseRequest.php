<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
            'category_id'           => [''],
            'title'                 => [''],
            'description'           => [''],
            'price'                 => [''],
            'price_sale'            => [''],
            'target_students'       => [''],
            'learning_outcomes'     => [''],
            'prerequisites'         => [''],
            'who_is_this_for'       => [''],
            'admin_commission_rate' => [''],
            'thumbnail'             => [''],
            'language'              => [''],
            'level'                 => [''],
            'primary_content'       => [''],
            'updated_at'            => [''],
        ];
    }
}
