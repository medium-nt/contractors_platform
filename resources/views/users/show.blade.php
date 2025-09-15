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

                <form action="{{ route('experts.update', ['user' => $user->id]) }}"
                      enctype="multipart/form-data"
                      method="POST">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" id="name"
                                   name="name" value="{{ $user->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" class="form-control" id="last_name"
                                   name="last_name" value="{{ $user->last_name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email"
                                   name="email" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" class="form-control" id="phone"
                                   name="phone" value="{{ $user->phone }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="wats_app">WatsApp</label>
                            <input type="text" class="form-control" id="wats_app"
                                   name="wats_app" value="{{ $user->wats_app }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="telegram">Telegram</label>
                            <input type="text" class="form-control" id="telegram"
                                   name="telegram" value="{{ $user->telegram }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="description">О себе</label>
                            <textarea class="form-control" id="description" name="description"
                                      rows="5" placeholder="не заполнено" disabled>{{ $user->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="hidden_field">Скрытое поле</label>
                            <textarea id="hidden_field"
                                      name="hidden_field"
                                      class="form-control @error('hidden_field') is-invalid @enderror"
                                      rows="5">{{ old('hidden_field', $user->hidden_field) }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <strong> Всего заказов: {{ $user->orders()->count() }} </strong> <br> <br>
                        В работе: {{ $user->orders()->whereIn('status_id', [2, 3, 4])->count() }} <br>
                        На гарантии: {{ $user->orders()->where('status_id', 5)->count() }} <br>
                        Завершено: {{ $user->orders()->where('status_id', 6)->count() }}
                    </div>
                    <div class="form-group">
                        <a href="{{ route('orders.create', ['expert_id' => $user->id]) }}" class="btn btn-primary">
                            Создать заказ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
