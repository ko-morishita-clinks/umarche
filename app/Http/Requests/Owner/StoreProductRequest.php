<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'price'       => 'required|integer|min:1',
            'sort_order'  => 'nullable|integer',
            'quantity'    => 'required|integer|min:1',
            'shop_id'     => 'required|exists:shops,id',
            'category'    => 'required|exists:secondary_categories,id',
            'image1'      => 'nullable|exists:images,id',
            'image2'      => 'nullable|exists:images,id',
            'image3'      => 'nullable|exists:images,id',
            'image4'      => 'nullable|exists:images,id',
            'is_selling'  => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名は必須です。',
            'price.min'     => '価格は1円以上で入力してください。',
            'quantity.min'  => '在庫数は1以上で入力してください。',
        ];
    }
}
