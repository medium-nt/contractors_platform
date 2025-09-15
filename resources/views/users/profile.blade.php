@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-6">
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

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="card-body">

                    @if($user->tg_id == '')
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading">Telegram не подключен</h4>
                            Для получения уведомлений через Telegram необходимо
                            <a href="{{ config('telegram.bots.mybot.link') }}" target="_blank">подключить бота</a>
                            <br>
                            <a href="{{ route('profile') }}">(проверить подключение)</a>
                        </div>
                    @else
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading">Telegram подключен</h4>
                            Для отключения уведомлений через Telegram <a href="{{ route('profile.disconnectTg') }}">нажмите тут</a>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input type="text" class="form-control" id="name" value="{{ $user->name }}"
                               name="name" placeholder="Имя" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        <input type="text" class="form-control" id="last_name" value="{{ $user->last_name }}"
                               name="last_name" placeholder="Фамилия" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                               name="email" placeholder="Email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="text" class="form-control" id="phone" value="{{ $user->phone }}"
                               name="phone" placeholder="Телефон">
                    </div>

                    <div class="form-group">
                        <label for="wats_app">WatsApp</label>
                        <input type="text" class="form-control" id="wats_app" value="{{ $user->wats_app }}"
                               name="wats_app" placeholder="WatsApp">
                    </div>

                    <div class="form-group">
                        <label for="telegram">Telegram</label>
                        <input type="text" class="form-control" id="telegram" value="{{ $user->telegram }}"
                               name="telegram" placeholder="Telegram">
                    </div>

                    <div class="form-group">
                        <label for="password">Новый пароль</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Пароль">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Подтверждение пароля</label>
                        <input type="password" class="form-control" id="password_confirmation"
                               name="password_confirmation" placeholder="Подтверждение пароля">
                    </div>

                    <div class="form-group">
                        <label for="avatar">Аватар</label>
                        <div class="row">
                            <div class="col-md-11 mt-2">
                                <input class="form-control" type="file" name="avatar" accept="image/*">
                            </div>
                            <div class="col-md-1">
                                @if($user->avatar != null)
                                    <img src="{{ asset('storage/' . $user->avatar) }}?v={{ time() }}"
                                         style="width:50px; height:50px;" alt="">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">О себе</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="5" placeholder="О себе">{{ $user->description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="subjects">Дисциплины</label>
                        <select class="form-control choices" name="subjects[]" multiple>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                {{ in_array($subject->id, $selectedSubjects) ? 'selected' : '' }}>
                                {{ $subject->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="types_work">Типы работ</label>
                        <select class="form-control choices" name="types_work[]" multiple>
                            @foreach($types_works as $type_work)
                                <option value="{{ $type_work->id }}"
                                    {{ in_array($type_work->id, $selectedTypesWork) ? 'selected' : '' }}>
                                    {{ $type_work->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        document.querySelectorAll('.choices').forEach(el => {
            new Choices(el, {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: false,
                noResultsText: 'Ничего не найдено',
                noChoicesText: 'Нет доступных вариантов',
                itemSelectText: 'Нажмите, чтобы выбрать',
                placeholderValue: 'Выберите...'
            });
        });
    </script>
@endpush
