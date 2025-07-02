<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create()
    {
        return view('tasks.create', [
            'title' => 'Добавить задачу'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5|max:255',
            'deadline_at' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
        ], [
            'title.required' => 'Поле "Текст задачи" обязательно для заполнения',
            'title.min' => 'В поле "Текст задачи" должно быть не менее 5 символов',
            'title.max' => 'В поле "Текст задачи" должно быть не более 255 символов',

            'deadline_at.required' => 'Поле "Срок выполнения" обязательно для заполнения',
            'deadline_at.date' => 'Поле "Срок выполнения" должно быть датой',
            'deadline_at.after_or_equal' => 'Поле "Срок выполнения" должно быть больше или равно текущей дате',
        ]);

        Task::query()->create([
            'title' => $request->title,
            'manager_id' => auth()->user()->id,
            'deadline_at' => $request->deadline_at,
        ]);

        return redirect()->route('calendar.index')->with('success', 'Задача добавлена');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', [
            'task' => $task,
            'title' => 'Редактировать задачу'
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|min:5|max:255',
            'deadline_at' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
        ], [
            'title.required' => 'Поле "Текст задачи" обязательно для заполнения',
            'title.min' => 'В поле "Текст задачи" должно быть не менее 5 символов',
            'title.max' => 'В поле "Текст задачи" должно быть не более 255 символов',

            'deadline_at.required' => 'Поле "Срок выполнения" обязательно для заполнения',
            'deadline_at.date' => 'Поле "Срок выполнения" должно быть датой',
            'deadline_at.after_or_equal' => 'Поле "Срок выполнения" должно быть больше или равно текущей дате',
        ]);

        $task->title = $request->title;
        $task->deadline_at = $request->deadline_at;
        $task->save();

        return redirect()->route('calendar.index')->with('success', 'Задача обновлена');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('calendar.index')->with('success', 'Задача удалена');
    }

    public function complete(Task $task)
    {
        $task->completed_at = now();
        $task->save();

        return redirect()->route('calendar.index')->with('success', 'Задача выполнена');
    }
}
