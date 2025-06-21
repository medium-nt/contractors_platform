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

            <form action="{{ route('orders.store') }}" method="POST">
                @method('POST')
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Название работы</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               placeholder=""
                               value="{{ old('title') }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание работы</label>
                        <textarea class="form-control  @error('description') is-invalid @enderror"
                                  name="description"
                                  id="description"
                                  rows="5"
                                  required
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="type_work_id">Тип работы</label>
                            <select name="type_work_id" id="type_work_id" class="form-control" required>
                                <option value="" disabled selected>---</option>
                                @foreach($typeWorks as $typeWork)
                                    <option value="{{ $typeWork->id }}"
                                        {{ old('type_work_id') == $typeWork->id ? 'selected' : '' }}>
                                        {{ $typeWork->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="subject_id">Название предмета</label>
                            <select name="subject_id" id="subject_id" class="form-control" required>
                                <option value="" disabled selected>---</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="text_uniqueness">Уникальность</label>
                            <input type="number"
                                   class="form-control @error('text_uniqueness') is-invalid @enderror"
                                   id="text_uniqueness"
                                   name="text_uniqueness"
                                   min="1"
                                   max="100"
                                   placeholder=""
                                   value="{{ old('text_uniqueness') }}"
                                   required>
                        </div>

                        <div class="form-group col-md-9">
                            <label for="plagiarism_platform_id">Платформа проверки на плагиат</label>
                            <select name="plagiarism_platform_id" id="plagiarism_platform_id" class="form-control">
                                <option value="" disabled selected>---</option>
                                @foreach($plagiarismPlatforms as $plagiarismPlatform)
                                    <option value="{{ $plagiarismPlatform->id }}"
                                        {{ old('plagiarism_platform_id') == $plagiarismPlatform->id ? 'selected' : '' }}>
                                        {{ $plagiarismPlatform->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="price">Цена</label>
                            <input type="number"
                                   class="form-control @error('price') is-invalid @enderror"
                                   id="price"
                                   name="price"
                                   min="1"
                                   placeholder=""
                                   value="{{ old('price') }}"
                                   required>
                        </div>

                        <div class="form-group col-md-9">
                            <label for="hidden_field">Скрытое поле</label>
                            <input type="text"
                                   class="form-control @error('hidden_field') is-invalid @enderror"
                                   id="hidden_field"
                                   name="hidden_field"
                                   placeholder=""
                                   value="{{ old('hidden_field') }}"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="deadline_at">Дата и время сдачи</label>
                            <input type="datetime-local"
                                   class="form-control @error('deadline_at') is-invalid @enderror"
                                   id="deadline_at"
                                   name="deadline_at"
                                   placeholder=""
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   value="{{ old('deadline_at') }}"
                                   required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="warranty_up_to">Гарантия до</label>
                            <input type="datetime-local"
                                   class="form-control @error('warranty_up_to') is-invalid @enderror"
                                   id="warranty_up_to"
                                   name="warranty_up_to"
                                   placeholder=""
                                   min="{{ now()->addDay()->format('Y-m-d\TH:i') }}"
                                   max="{{ now()->addMonth()->format('Y-m-d\TH:i') }}"
                                   value="{{ old('warranty_up_to') }}"
                                   required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="expert_id">Персонально для эксперта:</label>
                            <select name="expert_id" id="expert_id" class="form-control">
                                <option value="" disabled selected>---</option>
                                @foreach($experts as $expert)
                                    <option value="{{ $expert->id }}"
                                        {{ old('expert_id') == $expert->id ? 'selected' : '' }}>
                                        {{ $expert->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
