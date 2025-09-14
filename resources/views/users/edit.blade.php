@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="row">
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

                <form action="{{ route('users.update', ['user' => $user->id]) }}"
                      enctype="multipart/form-data"
                      method="POST">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" id="name"
                                   name="name" value="{{ $user->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" class="form-control" id="last_name"
                                   name="last_name" value="{{ $user->last_name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email"
                                   name="email" value="{{ $user->email }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" class="form-control" id="phone"
                                   name="phone" value="{{ $user->phone }}">
                        </div>

                        <div class="form-group">
                            <label for="wats_app">WatsApp</label>
                            <input type="text" class="form-control" id="wats_app"
                                   name="wats_app" value="{{ $user->wats_app }}">
                        </div>

                        <div class="form-group">
                            <label for="telegram">Telegram</label>
                            <input type="text" class="form-control" id="telegram"
                                   name="telegram" value="{{ $user->telegram }}">
                        </div>

                        @if(!$has_orders && $user->role_id != 3)
                        <div class="form-group">
                            <label for="role_id">Роль</label>
                            <select name="role_id" id="role_id" class="form-control" required>
                                <option value="1" @if($user->role_id == 1) selected @endif>Менеджер</option>
                                <option value="2" @if($user->role_id == 2) selected @endif>Эксперт</option>
                            </select>
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="password">новый пароль</label>
                            <input type="password" class="form-control" id="password"
                                   name="password">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Подтверждение пароля</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" placeholder="Подтверждение пароля">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
