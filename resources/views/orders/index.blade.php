@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">
                <form action="{{ route('orders.index') }}" method="get">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <select name="type_work_id"
                                    id="type_work_id"
                                    class="form-control"
                                    required>
                                <option value="all" selected>Все</option>
                                @foreach($typeWorks as $typeWork)
                                    <option value="{{ $typeWork->id }}"
                                            @if(request()->get('type_work_id') == $typeWork->id) selected @endif>
                                        {{ $typeWork->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <select name="subject_id"
                                    id="subject_id"
                                    class="form-control"
                                    required>
                                <option value="all" selected>Все</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                            @if(request()->get('subject_id') == $subject->id) selected @endif>
                                        {{ $subject->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <input type="text"
                                   name="search"
                                   id="search"
                                   class="form-control"
                                   value="{{ request()->get('search') }}">
                        </div>

                        <div class="form-group col-md-2">

                            <input type="hidden" name="status" value="{{ request()->get('status') }}">

                            <button type="submit"
                                    class="btn btn-primary mr-3"
                                    id="search-btn">
                                Поиск
                            </button>
                            <a href="{{ route('orders.index') }}" class="btn btn-default">Сбросить</a>
                        </div>
                    </div>
                </form>

                <div class="row">

                    @foreach($statuses as $status)
                    <a href="{{ route('orders.index', [
                        'status' => $status->id,
                        'type_work_id' => request('type_work_id'),
                        'subject_id' => request('subject_id'),
                        'search' => request('search'),
                    ]) }}"
                       class="btn btn-link">{{ Str::ucfirst($status->title) }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <a href="{{ route('orders.create') }}" class="btn btn-primary mr-3 mb-3">Добавить заказ</a>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Статус</th>
                            <th scope="col">Название</th>
                            <th scope="col">Тип работы</th>
                            <th scope="col">Предмет</th>
                            <th scope="col">Создан</th>
                            <th scope="col">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $order->status->color }}"> {{ $order->status->title }}</span></td>
                                <td>{{ $order->title }}</td>
                                <td>{{ $order->typeWork->title }}</td>
                                <td>{{ $order->subject->title }}</td>
                                <td>{{ $order->created_date }}</td>
                                <td>
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Заказы не найдены</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <x-pagination-component :collection="$orders" />

            </div>
        </div>
    </div>
@stop
