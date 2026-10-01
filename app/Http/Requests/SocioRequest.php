<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ChileanPhone;

class SocioRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        // Obtenemos el ID del socio si estamos en modo "update" (editar)
        $socioId = $this->route('socio') ? $this->route('socio')->id : null;

        $rules = [
            'rut' => 'required|string|cl_rut|unique:socios,rut,' . $socioId,
            'nombre' => 'required|string|max:255',
            'domicilio' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'telefono' => ['nullable', 'string', new ChileanPhone()],
            'email' => 'nullable|email|unique:socios,email,' . $socioId,
            'profesion' => 'nullable|string|max:255',
            'edad' => 'nullable|integer|min:0',
            'estado_civil' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ];

        if (!$socioId) {
            // Regla exclusiva para la creación (store)
            $rules['email'] .= '|unique:users,email';
        } else {
            // Regla exclusiva para la actualización (update)
            $rules['estado'] = 'required|string';
        }

        return $rules;
    }
}
