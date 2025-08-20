@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-12">
        <div class="card">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('orders.update', ['order' => $order->id]) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="card-body">

                    <div class="form-group">
                        <a href="{{ route('orders.show', ['order' => $order->id]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Назад
                        </a>
                    </div>

                    <div class="form-group">
                        <label for="title">Название работы</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               placeholder=""
                               value="{{ old('title', $order->title) }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание работы</label>
                        <textarea class="form-control  @error('description') is-invalid @enderror"
                                  name="description"
                                  id="description"
                                  rows="10"
                                  required
                        >{{ old('description', $order->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="type_work_id">Тип работы</label>
                            <select name="type_work_id" id="type_work_id" class="form-control select2" required>
                                <option value="" disabled selected>---</option>
                                @foreach($typeWorks as $typeWork)
                                    <option value="{{ $typeWork->id }}"
                                        {{ old('type_work_id', $order->type_work_id) == $typeWork->id ? 'selected' : '' }}>
                                        {{ $typeWork->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="subject_id">Название предмета</label>
                            <select name="subject_id" id="subject_id" class="form-control select2" required>
                                <option value="" disabled selected>---</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ old('subject_id', $order->subject_id) == $subject->id ? 'selected' : '' }}>
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
                                   value="{{ old('text_uniqueness', $order->text_uniqueness) }}">
                        </div>

                        <div class="form-group col-md-9">
                            <label for="plagiarism_platform_id">Платформа проверки на плагиат</label>
                            <select name="plagiarism_platform_id" id="plagiarism_platform_id" class="form-control">
                                <option value="" disabled selected>---</option>
                                @foreach($plagiarismPlatforms as $plagiarismPlatform)
                                    <option value="{{ $plagiarismPlatform->id }}"
                                        {{ old('plagiarism_platform_id', $order->plagiarism_platform_id) == $plagiarismPlatform->id ? 'selected' : '' }}>
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
                                   min="0"
                                   placeholder=""
                                   value="{{ old('price', $order->price) }}">
                        </div>

                        <div class="form-group col-md-9">
                            <label for="hidden_field">Скрытое поле</label>
                            <input type="text"
                                   class="form-control @error('hidden_field') is-invalid @enderror"
                                   id="hidden_field"
                                   name="hidden_field"
                                   placeholder=""
                                   value="{{ old('hidden_field', $order->hidden_field) }}">
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
                                   value="{{ old('deadline_at', \Carbon\Carbon::parse($order->deadline_at)->format('Y-m-d\TH:i') ) }}"
                                   required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="warranty_up_to">Гарантия до</label>
                            <input type="date"
                                   class="form-control @error('warranty_up_to') is-invalid @enderror"
                                   id="warranty_up_to"
                                   name="warranty_up_to"
                                   placeholder=""
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   value="{{ old('warranty_up_to', \Carbon\Carbon::parse($order->warranty_up_to)->format('Y-m-d') ) }}"
                                   required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="expert_id">Персонально для эксперта:</label>
                            <select name="expert_id" id="expert_id" class="form-control">
                                <option value="" disabled selected>---</option>
                                @foreach($experts as $expert)
                                    <option value="{{ $expert->id }}"
                                        {{ old('expert_id', $order->expert_id) == $expert->id ? 'selected' : '' }}>
                                        {{ $expert->name }} {{ $expert->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="form-group col-md-8">
                            <label for="task">Задача</label>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="task">Срок</label>
                        </div>
                    </div>

                    <div id="tasks">
                        @php
                            $tasksIds = old('task_ids', $order->tasks);
                            $tasks = old('task', $order->tasks);
                            $deadlines = old('deadline_task', $order->tasks);
                            $count = max(count($tasks), count($deadlines), count($tasksIds));
                        @endphp

                        @for ($i = 0; $i < $count; $i++)
                            <input type="hidden" name="task_ids[]" value="{{ $tasksIds[$i]->id ?? $tasksIds[$i] ?? null }}">
                            <div class="row">
                                <div class="form-group col-md-8">
                                    <input type="text"
                                           class="form-control @error("task.$i") is-invalid @enderror"
                                           name="task[]"
                                           value="{{ old('task.' . $i, $tasks[$i]->title ?? $tasks[$i]) }}"
                                           required>
                                    @error("task.$i")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <input type="datetime-local"
                                           class="form-control @error("deadline_task.$i") is-invalid @enderror"
                                           name="deadline_task[]"
                                           value="{{ old('deadline_task.' . $i, $deadlines[$i]->deadline_at ?? $deadlines[$i]) }}"
                                           required>
                                    @error("deadline_task.$i")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-1">
                                    <button type="button"
                                            class="btn btn-danger"
                                            onclick="removeTask(this)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <button type="button"
                            class="btn btn-success"
                            onclick="addTask()">
                        <i class="fas fa-plus"></i>
                    </button>

                    <hr>

                    <div class="row">
                        <div class="form-group">
                            <label for="files">Добавить файлы к заказу</label>
                            <input type="file"
                                   accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.doc,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                   class="form-control"
                                   id="files"
                                   name="files[]"
                                   multiple>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xl-3 col-md-6 col-sm-12">
                            <label for="comment">Загруженные файлы:</label>
                            <ul class="list-group">
                                @foreach($files as $file)
                                    <div class="file">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="{{ route('orders.download', ['order' => $order->id, 'name' => $file['name']]) }}" target="_blank">
                                                {{ $file['name'] }}
                                            </a>
                                            <a href="{{ route('orders.delete', ['order' => $order->id, 'name' => $file['name']]) }}"
                                               class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </li>
                                    </div>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $('a.btn-danger').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var container = button.closest('.file');

            button.hide();
            button.after('<span class="deleting"><i class="fas fa-spinner fa-pulse mr-1"></i>Удаление...</span>');

            $.ajax({
                url: button.attr('href'),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    container.remove();
                },
                error: function() {
                    button.next('.deleting').remove();
                    button.show();
                    alert('Ошибка при удалении файла');
                }
            });
        });

        $('button[type="submit"]').on('click', function() {
            var button = $(this);
            button.hide();
            button.after('<span class="saving"><i class="fas fa-spinner fa-pulse mr-1"></i>Идет сохранение...</span>');

            setTimeout(function() {
                button.next('.saving').remove();
                button.show();
            }, 5000);
        });
    </script>

    <script>
        function addTask() {
            let row = `
                <div class="row">
                    <div class="form-group col-md-8">
                        <input type="text"
                               class="form-control"
                               id="task"
                               name="task[]"
                               placeholder=""
                               value=""
                               required>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="datetime-local"
                               class="form-control"
                               id="deadline_task"
                               name="deadline_task[]"
                               placeholder=""
                               value=""
                               required>
                    </div>
                    <div class="form-group col-md-1">
                        <button type="button"
                                class="btn btn-danger"
                                id="remove-task"
                                name="remove-task"
                                onclick="removeTask(this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#tasks').append(row);
        }

        function removeTask(button) {
            $(button).parent().parent().remove();
        }
    </script>

    <script src="{{ asset('js/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js"></script>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush
