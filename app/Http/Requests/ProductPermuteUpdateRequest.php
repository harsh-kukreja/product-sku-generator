<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPermuteUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_price' => 'required|integer',
            'product_description' => 'required|string',
            'product_stock' => 'required|integer',
            'product_image' => 'image|mimes:jpeg,png,jpg'
        ];
    }
}
