<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['subjects'] = Subject::query()->paginate(20);
        $data['title'] = 'Названия предметов';

        return view('subjects.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subjects.create', [
            'title' => 'Добавить предмет'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $subject = new Subject();
        $subject->title = $request->title;
        $subject->save();

        return redirect()->route('subjects.index')->with('success', 'Тип работ добавлен');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', [
            'subject' => $subject,
            'title' => 'Редактировать предмет'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $subject->title = $request->title;
        $subject->save();

        return redirect()->route('subjects.index')->with('success', 'Название предмета обновлено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Тип работ удален');
    }
}
