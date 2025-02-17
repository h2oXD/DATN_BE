<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'category_id'               => ['nullable', Rule::exists('categories', 'id')],
            'title'                     => ['nullable', 'string', 'max:255'],
            'description'               => ['nullable', 'string'],
            'price_regular'             => ['nullable', 'integer', 'min:0'],
            'price_sale'                => ['nullable', 'integer', 'min:0', 'lte:price_regular'],
            'target_students'           => ['nullable', 'string'],
            'learning_outcomes'         => ['nullable', 'json'],
            'prerequisites'             => ['nullable', 'string'],
            'who_is_this_for'           => ['nullable', 'string'],
            'admin_commission_rate'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'thumbnail'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'video_preview'             => ['nullable', 'file', 'mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime'],
            'language'                  => ['nullable', 'string'],
            'level'                     => ['nullable', 'string'],
            'primary_content'           => ['nullable', 'string'],
        ];
    }
    public function messages(): array
    {
        return [
            'category_id.required' => 'Danh mục khóa học là bắt buộc.',
            'category_id.exists' => 'Danh mục khóa học không tồn tại.',
            'title.required' => 'Tiêu đề khóa học là bắt buộc.',
            'title.string' => 'Tiêu đề khóa học phải là chuỗi.',
            'title.max' => 'Tiêu đề khóa học không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả khóa học phải là chuỗi.',
            'price_regular.integer' => 'Giá khóa học phải là số nguyên.',
            'price_regular.min' => 'Giá khóa học phải lớn hơn hoặc bằng 0.',
            'price_sale.integer' => 'Giá khuyến mãi phải là số nguyên.',
            'price_sale.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0.',
            'price_sale.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',
            'learning_outcomes.json' => 'Kết quả học tập phải là chuỗi JSON hợp lệ.',
            'admin_commission_rate.numeric' => 'Tỷ lệ hoa hồng phải là số.',
            'admin_commission_rate.min' => 'Tỷ lệ hoa hồng phải lớn hơn hoặc bằng 0.',
            'admin_commission_rate.max' => 'Tỷ lệ hoa hồng phải nhỏ hơn hoặc bằng 100.',
            'thumbnail.image' => 'Hình ảnh phải là một tệp tin hình ảnh.',
            'thumbnail.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Lỗi validation.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
