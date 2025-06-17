@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <a href="{{ route('users.create') }}" class="btn btn-primary mr-3 mb-3">Добавить сотрудника</a>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Имя</th>
                            <th scope="col">Фамилия</th>
                            <th scope="col">email</th>
                            <th scope="col">Роль</th>
                            <th scope="col">Создан</th>
                            <th scope="col">Обновлен</th>
                            <th scope="col">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)

                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role->title }}</td>
                                <td>{{ $user->created_date }}</td>
                                <td>{{ $user->updated_date }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                    <a href="{{ route('users.edit', ['user' => $user->id]) }}"
                                       title="Редактировать"
                                       class="btn btn-primary mr-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', ['user' => $user->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger mr-1"
                                                onclick="return confirm('Вы уверены что хотите удалить данного сотрудника?')"
                                                title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @if(!$user->is_approved)
                                        <form action="{{ route('users.approve', ['user' => $user->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success mr-1"
                                                    onclick="return confirm('Вы уверены что хотите одобрить данного сотрудника?')"
                                                    title="Одобрить">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                {{-- Pagination --}}
                <x-pagination-component :collection="$users" />

            </div>
        </div>
    </div>
@stop
