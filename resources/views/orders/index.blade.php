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

                        <div class="form-group col-md-4">

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
                        @if(auth()->user()->role->name == 'expert' && $status->id > 4)
                            @continue
                        @endif
                    <a href="{{ route('orders.index', [
                        'status' => $status->id,
                        'type_work_id' => request('type_work_id'),
                        'subject_id' => request('subject_id'),
                        'search' => request('search'),
                    ]) }}"
                       class="btn btn-link">{{ Str::ucfirst($status->title) }}</a>
                    @endforeach
                    @if(auth()->user()->role->name == 'expert')
                        <a href="{{ route('orders.index', [
                            'status' => 10,
                            'type_work_id' => request('type_work_id'),
                            'subject_id' => request('subject_id'),
                            'search' => request('search'),
                        ]) }}"
                           class="btn btn-link">Готово</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                @if(auth()->user()->role->name == 'manager')
                    <a href="{{ route('orders.create') }}" class="btn btn-primary mr-3 mb-3">Добавить заказ</a>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Статус</th>
                            <th scope="col">Название</th>
                            <th scope="col">Тип работы</th>
                            <th scope="col">Предмет</th>
                            @switch(auth()->user()->role->name)
                                @case('manager')
                                    <th scope="col">ФИО эксперта</th>
                                    @break
                                @case('expert')
                                    <th scope="col">ФИО менеджера</th>
                                    @break
                                @case('admin')
                                    <th scope="col">ФИО эксперта</th>
                                    <th scope="col">ФИО менеджера</th>
                                    @break
                            @endswitch
                            <th scope="col">Создан</th>
                            <th scope="col">Дедлайн</th>
                            <th scope="col">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>
                                    @if(auth()->user()->role->name == 'expert' && ($order->status_id == 5 || $order->status_id == 6))
                                        <span class="badge" style="background-color: #28a745">готово</span>
                                    @else
                                        <span class="badge" style="background-color: {{ $order->status->color }}"> {{ $order->status->title }}</span>
                                    @endif
                                </td>
                                <td>{{ $order->title }}</td>
                                <td>{{ $order->typeWork->title }}</td>
                                <td>{{ $order->subject->title }}</td>
                                @switch(auth()->user()->role->name)
                                    @case('manager')
                                        <td>{{$order->expert->name ?? ''}} {{$order->expert->last_name ?? ''}}</td>
                                        @break
                                    @case('expert')
                                        <td>{{$order->manager->name ?? ''}} {{$order->manager->last_name ?? ''}}</td>
                                        @break
                                    @case('admin')
                                        <td>{{$order->expert->name ?? ''}} {{$order->expert->last_name ?? ''}}</td>
                                        <td>{{$order->manager->name ?? ''}} {{$order->manager->last_name ?? ''}}</td>
                                        @break
                                @endswitch
                                <td>{{ $order->created_date }}</td>
                                <td>{{ $order->deadline_date }}</td>
                                <td>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info">
                                        <i class="far fa-eye"></i>
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
