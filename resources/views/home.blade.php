@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')

    @if(!auth()->user()->is_approved)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Ваша учетная запись еще не одобрена админом.
        </div>
    @endif

    @php
        $users_count = $orders_count = 0;
    @endphp

    @if(auth()->user()->role->name == 'admin' || auth()->user()->role->name == 'manager')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Статистика менеджеров</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Менеджер</th>
                        <th scope="col" class="text-center">всего</th>
                        <th scope="col" class="text-center">в работе</th>
                        <th scope="col" class="text-center">на доработке</th>
                        <th scope="col" class="text-center">на гарантий</th>
                        <th scope="col" class="text-center">сдано</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($managersOrders as $managerOrders)
                    <tr>
                        <td>{{ $managerOrders['name'] }}</td>
                        <td class="text-center">{{ $managerOrders['all'] }}</td>
                        <td class="text-center">{{ $managerOrders['inWork'] }}</td>
                        <td class="text-center">{{ $managerOrders['inFixing'] }}</td>
                        <td class="text-center">{{ $managerOrders['warranty'] }}</td>
                        <td class="text-center">{{ $managerOrders['done'] }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Статистика экспертов</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Эксперт</th>
                        <th scope="col" class="text-center">всего</th>
                        <th scope="col" class="text-center">в работе</th>
                        <th scope="col" class="text-center">на доработке</th>
                        <th scope="col" class="text-center">на гарантий</th>
                        <th scope="col" class="text-center">сдано</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($expertsOrders as $expertOrders)
                        <tr>
                            <td>{{ $expertOrders['name'] }}</td>
                            <td class="text-center">{{ $expertOrders['all'] }}</td>
                            <td class="text-center">{{ $expertOrders['inWork'] }}</td>
                            <td class="text-center">{{ $expertOrders['inFixing'] }}</td>
                            <td class="text-center">{{ $expertOrders['warranty'] }}</td>
                            <td class="text-center">{{ $expertOrders['done'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@push('js')

@endpush

@push('css')

@endpush
