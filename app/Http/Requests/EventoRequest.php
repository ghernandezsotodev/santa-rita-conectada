<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class EventoRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_evento' => 'required|date',
            'lugar' => 'nullable|string|max:255',
        ];
    }
}
