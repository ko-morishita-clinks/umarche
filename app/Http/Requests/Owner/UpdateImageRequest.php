<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max'    => 'タイトルは50文字以内で入力してください。',
        ];
    }
}
