<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReadSaleRequest extends FormRequest
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
            'id'    => 'required|integer|exists:sales,id',
        ];
    }

    protected function prepareForValidation() : void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function messages() : array
    {
        return [
            'id.exists' => 'Sale not found',
        ];
    }
}
