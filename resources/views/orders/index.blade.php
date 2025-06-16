@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                Тут будет таблица заказов

{{--                <a href="{{ route('users.create') }}" class="btn btn-primary mr-3 mb-3">Добавить сотрудника</a>--}}

{{--                <div class="table-responsive">--}}
{{--                    <table class="table table-hover table-bordered">--}}
{{--                        <thead class="thead-dark">--}}
{{--                        <tr>--}}
{{--                            <th scope="col">#</th>--}}
{{--                            <th scope="col">Имя</th>--}}
{{--                            <th scope="col">Роль</th>--}}
{{--                            <th scope="col">email</th>--}}
{{--                            <th scope="col">Создан</th>--}}
{{--                            <th scope="col">Обновлен</th>--}}
{{--                            <th scope="col">Действия</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}

{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}

                {{-- Pagination --}}
{{--                <x-pagination-component :collection="$users" />--}}

            </div>
        </div>
    </div>
@stop
