<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaccionRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    /**
     * Sanitiza los datos ANTES de validarlos.
     */
    protected function prepareForValidation()
    {
        if ($this->has('monto')) {
            $this->merge([
                'monto' => preg_replace('/[^0-9]/', '', $this->monto)
            ]);
        }
    }

    public function rules()
    {
        return [
            'fecha' => 'required|date',
            'tipo' => 'required|in:Ingreso,Egreso',
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'required|string|max:255',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'socio_id' => 'nullable|exists:socios,id',
        ];
    }
}
