<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ActaRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $rules = [
            'titulo' => 'required|string|max:255',
            'fecha' => 'required|date',
            'contenido' => 'required|string',
        ];

        // Si es creación, el archivo es obligatorio. Si es edición, es opcional.
        $isUpdate = $this->route('acta') ? true : false;
        $rules['archivo'] = ($isUpdate ? 'nullable' : 'required') . '|file|mimes:pdf|max:20480';

        return $rules;
    }
}
