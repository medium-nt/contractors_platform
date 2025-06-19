@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <a href="{{ route('subjects.create') }}" class="btn btn-primary mr-3 mb-3">Добавить названия предметов</a>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название</th>
                            {{--                            <th scope="col">Активна</th>--}}
                            <th scope="col">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($subjects as $subject)
                            <tr>
                                <td>{{ $subject->id }}</td>
                                <td>{{ $subject->title }}</td>
                                {{--                                <td>{{ subject->is_active }}</td>--}}
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('subjects.edit', ['subject' => $subject->id]) }}"
                                           title="Редактировать"
                                           class="btn btn-primary mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('subjects.destroy', ['subject' => $subject->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mr-1"
                                                    onclick="return confirm('Вы уверены что хотите удалить данный тип работы?')"
                                                    title="Удалить">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        {{--                                        @if(subject->is_active)--}}
                                        {{--                                            <form action="{{ route('types_work.approve', ['user' => $type_work->id]) }}" method="POST">--}}
                                        {{--                                                @csrf--}}
                                        {{--                                                @method('PUT')--}}
                                        {{--                                                <button type="submit" class="btn btn-success mr-1"--}}
                                        {{--                                                        onclick="return confirm('Вы уверены что хотите одобрить данного сотрудника?')"--}}
                                        {{--                                                        title="Отключить">--}}
                                        {{--                                                    <i class="fas fa-check"></i>--}}
                                        {{--                                                </button>--}}
                                        {{--                                            </form>--}}
                                        {{--                                        @else--}}
                                        {{--                                            <form action="{{ route('users.approve', ['user' => $type_work->id]) }}" method="POST">--}}
                                        {{--                                                @csrf--}}
                                        {{--                                                @method('PUT')--}}
                                        {{--                                                <button type="submit" class="btn btn-success mr-1"--}}
                                        {{--                                                        onclick="return confirm('Вы уверены что хотите одобрить данного сотрудника?')"--}}
                                        {{--                                                        title="Одобрить">--}}
                                        {{--                                                    <i class="fas fa-check"></i>--}}
                                        {{--                                                </button>--}}
                                        {{--                                            </form>--}}
                                        {{--                                        @endif--}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <x-pagination-component :collection="$subjects" />

            </div>
        </div>
    </div>
@stop
