<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\TypeWork;
use App\Models\User;
use Illuminate\Http\Request;

class ExpertsController extends Controller
{
    public function index(Request $request)
    {
        $typeWorkId = $request->type_work_id ?? 'all';
        $subjectId = $request->subject_id ?? 'all';

        $users = User::query();

        if ($typeWorkId != 'all') {
            //  условие запроса
            $users = $users;
        }

        if ($subjectId != 'all') {
            //  условие запроса
            $users = $users;
        }

        return view('users.experts', [
            'title' => 'Эксперты',
            'typeWorks' => TypeWork::all(),
            'subjects' => Subject::all(),
            'users' => $users
                ->where('role_id', 2)
                ->withCount('orders')
                ->orderBy('orders_count', 'desc')
                ->paginate(10),
        ]);
    }

    public function show(User $user)
    {
        return view('users.show', [
            'title' => 'Профиль эксперта',
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'hidden_field' => 'nullable|string|min:5',
        ], [
            'hidden_field.string' => 'Комментарий должен быть строкой.',
            'hidden_field.min' => 'Комментарий должен быть не менее 5 символов.',
        ]);

        $user->update([
            'hidden_field' => $request->hidden_field,
        ]);

        return back()->with('success', 'Изменения сохранены.');
    }
}
