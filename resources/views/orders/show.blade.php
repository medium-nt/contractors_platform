@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="form-group">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary mr-3">
                        <i class="fas fa-arrow-left mr-1"></i>Назад
                    </a>
                    @if(auth()->user()->role->name == 'admin' || auth()->user()->role->name == 'manager')
                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary">
                            <i class="far fa-edit mr-1"></i> Редактировать
                        </a>
                    @endif
                </div>

                <div class="form-group">
                    <label for="title">Название работы</label>
                    <input type="text" class="form-control" placeholder="" value="{{ $order->title }}" disabled>
                </div>

                <div class="form-group">
                    <label for="description">Описание работы</label>
                    <textarea class="form-control" rows="5" disabled>{{ $order->description }}</textarea>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="type_work_id">Тип работы</label>
                        <input type="text" class="form-control" value="{{ $order->typeWork->title }}" disabled>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="subject_id">Название предмета</label>
                        <input type="text" class="form-control" value="{{ $order->subject->title }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="text_uniqueness">Уникальность</label>
                        <input type="number" class="form-control" placeholder="" value="{{ $order->text_uniqueness }}" disabled>
                    </div>

                    <div class="form-group col-md-9">
                        <label for="plagiarism_platform_id">Платформа проверки на плагиат</label>
                        <input type="text" class="form-control" value="{{ $order->plagiarismPlatform->title ?? '' }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="price">Цена</label>
                        <input type="number" class="form-control" placeholder="" value="{{ $order->price }}" disabled>
                    </div>

                    @if(auth()->user()->role->name != 'expert')
                    <div class="form-group col-md-9">
                        <label for="hidden_field">Скрытое поле</label>
                        <input type="text" class="form-control" placeholder="" value="{{ $order->hidden_field }}" disabled>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="deadline_at">Дата и время сдачи</label>
                        <input type="datetime-local"
                               class="form-control"
                               placeholder=""
                               value="{{ \Carbon\Carbon::parse($order->deadline_at)->format('Y-m-d\TH:i') }}"
                               disabled>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="warranty_up_to">Гарантия до</label>
                        <input type="date"
                               class="form-control"
                               placeholder=""
                               value="{{ \Carbon\Carbon::parse($order->warranty_up_to)->format('Y-m-d') }}"
                               disabled>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="expert_id">Эксперт:</label>
                        <input type="text" class="form-control" name="expert_id" value="{{ $order->expert->name ?? '' }}" disabled>
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
                        <div class="row">
                            <div class="form-group col-md-8">
                                <input type="text"
                                       class="form-control"
                                       value="{{ $tasks[$i]->title ?? $tasks[$i] }}"
                                       disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="datetime-local"
                                       class="form-control"
                                       value="{{ $deadlines[$i]->deadline_at ?? $deadlines[$i] }}"
                                       disabled>
                            </div>
                        </div>
                    @endfor
                </div>

                <hr>

                @if(auth()->user()->role->name == 'expert' && ($order->status_id == 2 || $order->status_id == 4) && $order->expert_id == auth()->user()->id)
                <div class="form-group">
                    <a href="{{ route('orders.complete', $order->id) }}" class="btn btn-success"
                            onclick="return confirm('Вы уверены что работа выполнена полностью?')">
                        Сдать выполненную работу
                    </a>
                </div>
                @endif

                @if(auth()->user()->role->name == 'expert' && $order->status_id == 1 && $order->expert_id == null)
                <div class="form-group">
                    <a href="{{ route('orders.take_to_work', $order->id) }}" class="btn btn-success"
                       onclick="return confirm('Вы уверены что хотите взять эту заявку в работу?')">
                        Взять в работу
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('js')
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
@stop
