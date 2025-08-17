@php
use Carbon\Carbon;
@endphp

@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@php
    $roleName = auth()->user()->role->name;
@endphp

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

            <div class="card-body">

                <div class="form-group d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">

                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary mr-3">
                        <i class="fas fa-arrow-left mr-1"></i>Назад
                    </a>
                    @if($roleName == 'admin' || $roleName == 'manager')
                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary">
                            <i class="far fa-edit mr-1"></i> Редактировать
                        </a>
                    @endif
                    </div>

                    @if(auth()->user()->role->name != 'expert')
                        <h5>
                            <span class="badge ml-auto xl" style="background-color: {{ $order->status->color }}">
                                {{ $order->status->title }}
                            </span>
                        </h5>
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

                    @if($roleName != 'expert')
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

                    @if(auth()->user()->role->name != 'expert')
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
                        <input type="text" class="form-control" name="expert_id" value="{{ $order->expert->name ?? '' }} {{ $order->expert->last_name ?? '' }}" disabled>
                    </div>
                    @endif
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

                <div class="row">
                    <div class="form-group col-xl-3 col-md-6 col-sm-12">
                        <label for="comment">Загруженные файлы:</label>
                        <ul class="list-group">
                            @foreach($files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('orders.download', ['order' => $order->id, 'name' => $file['name']]) }}" target="_blank">
                                        {{ $file['name'] }}
                                        ({{ Carbon::parse($file['modified'])->format('d/m/Y H:i') }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @if($files)
                        <a href="{{ route('orders.download_archive', ['order' => $order->id, 'folder' => 'order_files']) }}"
                           class="btn btn-primary mt-3">Скачать все</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($order->status_id > 1)
        <div class="row">
            <div class="form-group col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <label for="comment">Дополнительные файлы менеджера:</label>
                        <ul class="list-group">
                            @foreach($managerFiles as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center file">
                                    <a href="{{ route('orders.download_manager_file', ['order' => $order->id, 'name' => $file['name']]) }}" target="_blank">
                                        {{ $file['name'] }}
                                        ({{ Carbon::parse($file['modified'])->format('d/m/Y H:i') }})
                                    </a>

                                    @if($roleName == 'manager' && ($order->status_id == 2 || $order->status_id == 4) && $order->manager_id == auth()->user()->id)
                                        <a href="{{ route('orders.delete_file_manager', ['order' => $order->id, 'name' => $file['name']]) }}"
                                           class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        @if($managerFiles)
                        <a href="{{ route('orders.download_archive', ['order' => $order->id, 'folder' => 'manager_files']) }}"
                               class="btn btn-primary mt-3">Скачать все</a>
                        @endif
                        <hr>

                        @if($roleName == 'manager' && ($order->status_id == 2 || $order->status_id == 4) && $order->manager_id == auth()->user()->id)
                            <form method="POST"
                                  enctype="multipart/form-data"
                                  action="{{ route('orders.add_file_manager', $order->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="file">Загрузить новые файлы:</label>
                                    <input type="file" class="form-control" name="files[]"
                                           accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.doc,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                           multiple required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Загрузить</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <label for="comment">Дополнительные файлы эксперта:</label>
                        <ul class="list-group">
                            @foreach($expertFiles as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center file">
                                    <a href="{{ route('orders.download_expert_file', ['order' => $order->id, 'name' => $file['name']]) }}" target="_blank">
                                        {{ $file['name'] }}
                                        ({{ Carbon::parse($file['modified'])->format('d/m/Y H:i') }})
                                    </a>
                                    @if($roleName == 'expert' && ($order->status_id == 2 || $order->status_id == 4) && $order->expert_id == auth()->user()->id)
                                        <a href="{{ route('orders.delete_file_expert', ['order' => $order->id, 'name' => $file['name']]) }}"
                                           class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        @if($expertFiles)
                        <a href="{{ route('orders.download_archive', ['order' => $order->id, 'folder' => 'expert_files']) }}"
                           class="btn btn-primary mt-3">Скачать все</a>
                        @endif

                        <hr>

                        @if($roleName == 'expert' && ($order->status_id == 2 || $order->status_id == 4) && $order->expert_id == auth()->user()->id)
                            <form method="POST"
                                  enctype="multipart/form-data"
                                  action="{{ route('orders.add_file_expert', $order->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="file">Загрузить новые файлы:</label>
                                    <input type="file" class="form-control" name="files[]"
                                           accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.doc,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                           multiple required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Загрузить</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-body">

                @if($order->status_id > 2)
                <div class="row">
                    <div class="form-group col-xl-3 col-md-6 col-sm-12">
                        <label for="comment">Файлы результата:</label>
                        <ul class="list-group">
                            @foreach($resultFiles as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('orders.download_result', ['order' => $order->id, 'name' => $file['name']]) }}" target="_blank">
                                        {{ $file['name'] }}
                                        ({{ Carbon::parse($file['modified'])->format('d/m/Y H:i') }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @if($resultFiles)
                        <a href="{{ route('orders.download_archive', ['order' => $order->id, 'folder' => 'result_files']) }}"
                           class="btn btn-primary mt-3">Скачать все</a>
                        @endif
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-12">

                    @if($roleName == 'expert' && $order->status_id == 1 && $order->expert_id == null)
                        @if($response)
                            <div class="form-group">
                                <form method="POST" action="{{ route('orders.del_response', $order->id) }}">
                                    @method('POST')
                                    @csrf
                                    <div class="form-group">
                                        <label for="comment">Ваш комментарии к отклику:</label>
                                        <input type="text"
                                               class="form-control"
                                               value="{{ $response->comment }}"
                                               disabled>
                                    </div>
                                    <div class="form-group">
                                        <button onclick="return confirm('Вы уверены что хотите удалить свой отклик на данную заявку?')"
                                            class="btn btn-danger"
                                            type="submit"
                                        >Удалить отклик</button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="form-group">
                                <form method="POST" action="{{ route('orders.set_response', $order->id) }}">
                                    @method('POST')
                                    @csrf
                                    <div class="form-group">
                                        <label for="comment">Комментарии:</label>
                                        <input type="text"
                                               class="form-control @error('description') is-invalid @enderror"
                                               minlength="3"
                                               name="comment"
                                               value="{{ old('comment') }}"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success"
                                                onclick="return confirm('Вы уверены что хотите откликнуться на данную заявку?')"
                                        >Оставить отклик</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif

                    @if(($roleName == 'manager' || $roleName == 'admin') && $order->status_id == 1 && $order->expert_id == null)
                        <div class="form-group">
                            <label for="comment">Отклики экспертов:</label>
                            <ul class="list-group">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">Эксперт</th>
                                        <th scope="col">Комментарий</th>
                                        <th scope="col">Всего заданий</th>
                                        <th scope="col">В работе</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($responses as $response)
                                        <tr>
                                            <td>{{ $response->expert->name }}</td>
                                            <td>{{ $response->comment }}</td>
                                            <td>{{ $response->all_tasks }}</td>
                                            <td>{{ $response->working_tasks }}</td>
                                            <td>
                                                <a href="{{ route('orders.check_expert', ['order' => $order->id, 'expert' => $response->expert_id]) }}"
                                                   onclick="return confirm('Вы уверены что хотите выбрать данного эксперта?')"
                                                   class="btn btn-success btn">
                                                    <i class="fas fa-check mr-1"></i>Выбрать данного эксперта
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </ul>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('orders.change_status', ['order' => $order->id, 'status' => 7 ]) }}" class="btn btn-danger mr-3"
                               onclick="return confirm('Вы уверены что хотите отменить этот заказ?')">
                                Отмена
                            </a>
                            <a href="{{ route('orders.change_status', ['order' => $order->id, 'status' => 8 ]) }}" class="btn btn-danger"
                               onclick="return confirm('Вы уверены что хотите отменить этот заказ?')">
                                Отказ клиента
                            </a>
                        </div>
                    @endif

                    @if($roleName == 'manager' && $order->status_id == 3)
                        <div class="form-group">
                            <a href="{{ route('orders.change_status', ['order' => $order->id, 'status' => 4 ]) }}" class="btn btn-warning mr-3"
                               onclick="return confirm('Вы уверены что хотите вернуть этот заказ в доработку?')">
                                В доработку
                            </a>
                            <a href="{{ route('orders.change_status', ['order' => $order->id, 'status' => 5 ]) }}" class="btn btn-primary mr-3"
                               onclick="return confirm('Вы уверены что хотите отправить этот заказ на гарантию?')">
                                На гарантию
                            </a>
                            <a href="{{ route('orders.change_status', ['order' => $order->id, 'status' => 6 ]) }}" class="btn btn-success"
                               onclick="return confirm('Вы уверены что хотите отметить этот заказ успешно выполненным?')">
                                Закрыть как выполненный
                            </a>
                        </div>
                    @endif

                    @if($roleName == 'manager' && $order->status_id == 5)
                        <div class="form-group">
                            <a href="{{ route('orders.change_status', ['order' => $order->id, 'status' => 4 ]) }}" class="btn btn-warning mr-3"
                               onclick="return confirm('Вы уверены что хотите вернуть этот заказ в доработку?')">
                                В доработку
                            </a>
                        </div>
                    @endif

                    @if($roleName == 'expert' && ($order->status_id == 2 || $order->status_id == 4) && $order->expert_id == auth()->user()->id)
                    <form method="POST"
                          enctype="multipart/form-data"
                          action="{{ route('orders.complete', $order->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="file">Загрузить результат:</label>
                            <input type="file" class="form-control" name="files[]"
                                   accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.doc,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                   multiple required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Вы уверены что работа выполнена полностью?')"
                            >Сдать выполненную работу</button>
                        </div>
                    </form>
                    @endif
                    </div>
                </div>
            </div>
        </div>

        @if($order->status_id > 1)
        <div class="card">
            <div id="new-message-indicator" style="
                display: none;
                position: fixed;
                bottom: 100px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 1050;
            ">
                <button class="btn btn-warning btn-sm" onclick="scrollToBottom(true)">
                    Новое сообщение ↓
                </button>
            </div>

            <livewire:chat-component :order-id="$order->id" />
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Лог изменений</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Кем</th>
                        <th>Изменение</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($changeLog as $history)
                        <tr>
                            <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $history->user->name }} {{ $history->user->last_name }}</td>
                            <td>{{ $history->message }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
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
            }, 7500);
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
@stop
