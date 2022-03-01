<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeStore extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimetypes:text/plain,text/csv'],  
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => "Nenhum arquivo foi selecionado",
            'file.file' => "O campo file deve ser um arquivo",
            'file.mimetypes' => "O tipo de arquivo deve ser CSV"
        ];
    }
}
