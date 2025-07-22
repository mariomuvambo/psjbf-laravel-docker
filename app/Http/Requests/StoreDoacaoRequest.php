<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoacaoRequest extends FormRequest
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
            'doador' => 'nullable|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data' => 'required|date',
            'meio_pagamento' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ];
    }
}
