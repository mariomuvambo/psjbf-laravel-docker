<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCasamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
        'noivo_id' => 'required|exists:users,id',
        'noiva_id' => 'required|exists:users,id',
        'data_casamento' => 'required|date|after:today',
        'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ];
    }
}
