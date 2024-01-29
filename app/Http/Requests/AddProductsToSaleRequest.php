<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductsToSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() : array
    {
        return [
            'sale_id'           => 'required|integer|exists:sales,id',
            'products'          => 'required|array',
            'products.*.id'     => 'required|integer|exists:products,id',
            'products.*.amount' => 'required|integer|min:1',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'sale_id' => $this->route('id'),
        ]);
    }
}
