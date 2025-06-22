<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'title' => 'required|min:5|max:255',
            'description' => 'required|min:10|max:255',
            'hidden_field' => 'nullable|max:255',
            'price' => 'required|numeric|min:1',
            'type_work_id' => 'required|exists:types_work,id',
            'subject_id' => 'required|exists:subjects,id',
            'plagiarism_platform_id' => 'nullable|exists:plagiarism_platforms,id',
            'text_uniqueness' => 'nullable|numeric|min:1|max:100',
            'expert_id' => 'nullable|exists:users,id',
            'deadline_at' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
            'warranty_up_to' => 'required|date|after_or_equal:now',

            'task' => 'required|array',
            'task.*' => 'required|string|min:2|max:255',
            'deadline_task' => 'required|array',
            'deadline_task.*' => 'required|date|after_or_equal:now',

        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Поле "Имя" обязательно для заполнения',
            'title.min' => 'В поле "Имя" должно быть не менее 5 символов',
            'title.max' => 'В поле "Имя" должно быть не более 255 символов',

            'description.required' => 'Поле "Описание" обязательно для заполнения',
            'description.min' => 'В поле "Описание" должно быть не менее 10 символов',
            'description.max' => 'В поле "Описание" должно быть не более 255 символов',

            'hidden_field.max' => 'В поле "Скрытое поле" должно быть не более 255 символов',

            'price.required' => 'Поле "Цена" обязательно для заполнения',
            'price.numeric' => 'Поле "Цена" должно быть числом',
            'price.min' => 'В поле "Цена" должна быть не менее 1',

            'type_work_id.required' => 'Поле "Тип работы" обязательно для заполнения',
            'type_work_id.exists' => 'Указан неизвестный тип работы',

            'subject_id.required' => 'Поле "Название предмета" обязательно для заполнения',
            'subject_id.exists' => 'Указан неизвестный предмет',

            'plagiarism_platform_id.exists' => 'Указана не известная платформа проверки на плагиат',

            'text_uniqueness.numeric' => 'Поле "Уникальность текста" должно быть числом',
            'text_uniqueness.min' => 'Уникальность текста должна быть не менее 1%',
            'text_uniqueness.max' => 'Уникальность текста должна быть не более 100%',

            'expert_id.exists' => 'Указан неизвестный Эксперт',

            'deadline_at.required' => 'Поле "Дата и время сдачи" обязательно для заполнения',
            'deadline_at.date' => 'Поле "Дата и время сдачи" должно быть датой',
            'deadline_at.after_or_equal' => 'Поле "Дата и время сдачи" должно быть больше или равно текущей дате',

            'warranty_up_to.required' => 'Поле "Гарантия до" обязательно для заполнения',
            'warranty_up_to.date' => 'Поле "Гарантия до" должно быть датой',
            'warranty_up_to.after_or_equal' => 'Поле "Гарантия до" должно быть больше или равно текущей дате',

            'task.required' => 'Поле "Текст задачи" обязательно для заполнения',
            'task.*.required' => 'Поле "Текст задачи" обязательно для заполнения',
            'task.*.min' => 'В поле "Текст задачи" должно быть не менее 2 символов',
            'task.*.max' => 'В поле "Текст задачи" должно быть не более 255 символов',

            'deadline_task.required' => 'Поле "Срок выполнения задачи" обязательно для заполнения',
            'deadline_task.*.required' => 'Поле "Срок выполнения задачи" обязательно для заполнения',
            'deadline_task.*.date' => 'Поле "Срок выполнения задачи" должно быть датой',
            'deadline_task.*.after_or_equal' => 'Поле "Срок выполнения задачи" должно быть больше или равно текущей дате',
        ];
    }
}
