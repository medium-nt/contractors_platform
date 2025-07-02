@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-6">
        <div class="card">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="card-body">
                <div class="row">
                    <a href="{{ route('calendar.index') }}" class="btn btn-outline-secondary mb-3 mr-5">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Назад
                    </a>

                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                        @method('DELETE')
                        @csrf

                        <button type="submit" class="btn btn-outline-danger"
                                onclick="return confirm('Вы уверены что хотите удалить задачу?')">
                            <i class="fas fa-trash mr-1"></i>
                            Удалить задачу
                        </button>
                    </form>
                </div>

                <form action="{{ route('tasks.update', $task->id) }} }}" method="POST">
                    @method('PUT')
                    @csrf

                    <div class="form-group">
                        <label for="title">Текст задачи</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               minlength="5"
                               placeholder=""
                               value="{{ old('title', $task->title) }}"
                               required>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="deadline_at">Срок выполнения</label>
                            <input type="datetime-local"
                                   class="form-control @error('deadline_at') is-invalid @enderror"
                                   id="deadline_at"
                                   name="deadline_at"
                                   placeholder=""
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   value="{{ old('deadline_at', $task->deadline_at) }}"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success mr-5">Сохранить изменения</button>
                        </div>

                        <a href="{{ route('tasks.complete', $task->id) }}"
                           onclick="return confirm('Вы уверены что задача выполнена?')"
                           class="btn btn-outline-success mb-3 mr-5">
                            <i class="fas fa-check mr-1"></i>
                            Отметить выполненной
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
