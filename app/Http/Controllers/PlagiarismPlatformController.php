<?php

namespace App\Http\Controllers;

use App\Models\PlagiarismPlatform;
use Illuminate\Http\Request;

class PlagiarismPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['plagiarismPlatforms'] = PlagiarismPlatform::query()->paginate(20);
        $data['title'] = 'Платформы антиплагиата';

        return view('plagiarism_platform.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('plagiarism_platform.create', [
            'title' => 'Добавить платформу антиплагиата'
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

        $platform = new PlagiarismPlatform();
        $platform->title = $request->title;
        $platform->save();

        return redirect()->route('plagiarism_platforms.index')->with('success', 'Тип работ добавлен');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlagiarismPlatform $plagiarismPlatform)
    {
        return view('plagiarism_platform.edit', [
            'plagiarismPlatform' => $plagiarismPlatform,
            'title' => 'Редактировать платформу антиплагиата'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlagiarismPlatform $plagiarismPlatform)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $plagiarismPlatform->title = $request->title;
        $plagiarismPlatform->save();

        return redirect()->route('plagiarism_platforms.index')->with('success', 'Платформа антиплагиата обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlagiarismPlatform $plagiarismPlatform)
    {
        $plagiarismPlatform->delete();

        return redirect()->route('plagiarism_platforms.index')->with('success', 'Платформа антиплагиата удалена');
    }
}
