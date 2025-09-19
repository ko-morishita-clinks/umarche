<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShopRequest extends FormRequest
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
            'name'        => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'is_selling'  => 'required|boolean',
            'image'       => 'nullable|image|max:2048', // 例: 2MBまで
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => '店舗名は必須です。',
            'information.required' => '店舗情報は必須です。',
            'is_selling.required'  => '公開/非公開を選択してください。',
        ];
    }
}
