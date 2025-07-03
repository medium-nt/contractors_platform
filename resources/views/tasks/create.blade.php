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

            <form action="{{ route('tasks.store') }}" method="POST">
                @method('POST')
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-9">
                            <label for="title">Текст задачи</label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   minlength="5"
                                   placeholder=""
                                   value="{{ old('title') }}"
                                   required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="deadline_at">Срок выполнения</label>
                            <input type="datetime-local"
                                   class="form-control @error('deadline_at') is-invalid @enderror"
                                   id="deadline_at"
                                   name="deadline_at"
                                   placeholder=""
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   value="{{ old('deadline_at') }}"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание задачи</label>
                        <input type="text"
                               class="form-control @error('description') is-invalid @enderror"
                               id="description"
                               name="description"
                               placeholder=""
                               value="{{ old('description') }}"
                               required>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">Создать задачу</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
