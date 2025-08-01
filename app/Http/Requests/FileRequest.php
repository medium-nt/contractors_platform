<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => 'nullable|array',
            'files.*' => 'required|file|mimetypes:image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.doc,image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.doc,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Обязательно добавьте хотя бы один файл.',
            'files.array' => 'Обязательно добавьте хотя бы один файл.',

            'files.*.required' => 'Не загружено ни одного файла.',
            'files.*.file' => 'Вы пытаетесь загрузить не файл.',
            'files.*.mimetypes' => 'Допустимые форматы: изображения, PDF, DOC, DOCX.',
        ];
    }
}
