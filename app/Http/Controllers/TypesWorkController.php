<?php

namespace App\Http\Controllers;

use App\Models\TypeWork;
use Illuminate\Http\Request;

class TypesWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['types_work'] = TypeWork::query()->paginate(20);
        $data['title'] = 'Типы работ';

        return view('types_work.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('types_work.create', [
            'title' => 'Добавить тип работ'
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

        $types_work = new TypeWork();
        $types_work->title = $request->title;
        $types_work->save();

        return redirect()->route('types_work.index')->with('success', 'Тип работ добавлен');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeWork $type_work)
    {
        return view('types_work.edit', [
            'typeWork' => $type_work,
            'title' => 'Редактировать тип работ'
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeWork $type_work)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $type_work->title = $request->title;
        $type_work->save();

        return redirect()->route('types_work.index')->with('success', 'Тип работ обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeWork $type_work)
    {
        $type_work->delete();

        return redirect()->route('types_work.index')->with('success', 'Тип работ удален');
    }
}
