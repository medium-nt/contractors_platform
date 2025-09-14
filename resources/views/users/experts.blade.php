@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('experts.index') }}" method="get">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <select name="type_work_id"
                                    id="type_work_id"
                                    class="select2"
                                    required>
                                <option value="all" selected>---</option>
                                @foreach($typeWorks as $typeWork)
                                    <option value="{{ $typeWork->id }}"
                                            @if(request('type_work_id') == $typeWork->id) selected @endif>
                                        {{ $typeWork->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <select name="subject_id"
                                    id="subject_id"
                                    class="select2"
                                    required>
                                <option value="all" selected>---</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                            @if(request('subject_id') == $subject->id) selected @endif>
                                        {{ $subject->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <button type="submit"
                                    class="btn btn-primary mr-3"
                                    id="search-btn">
                                Поиск
                            </button>
                            <a href="{{ route('experts.index') }}" class="btn btn-default">Сбросить</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ФИО</th>
                            <th scope="col">Контактные данные</th>
                            <th scope="col">в работе</th>
                            <th scope="col">в корректировке</th>
                            <th scope="col">на гарантии</th>
                            <th scope="col">предметы</th>
                            <th scope="col">типы работ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td><a href="{{ route('experts.show', $user->id) }}"> {{ $user->name }} {{ $user->last_name }} </a></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->orders_in_work }}</td>
                                <td>{{ $user->orders_fixing }}</td>
                                <td>{{ $user->orders_warranty }}</td>
                                <td>
                                    @foreach($user->subjects as $subject)
                                        <li>{{ $subject->title }} </li>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($user->typeWorks as $typeWork)
                                        <li>{{ $typeWork->title }} </li>
                                    @endforeach
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

@section('js')
    <script src="{{ asset('js/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js"></script>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet" >
@endpush
