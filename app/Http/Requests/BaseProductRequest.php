<?php

namespace App\Http\Requests;

use App\Models\BaseProduct;
use Illuminate\Foundation\Http\FormRequest;

class BaseProductRequest extends FormRequest
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
            'product_name' => 'required|string',
            'product_price' => 'required|integer',
            'product_stock' => 'required|integer',
            'product_description' => 'required|string',
            'is_variant' => 'required|in:'.BaseProduct::VARIANT.','.BaseProduct::NO_VARIANT,
            'product_image' => 'required|image|mimes:jpeg,png,jpg'
        ];
    }
}
