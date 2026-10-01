<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ApiLoginRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string',
        ];
    }
}
