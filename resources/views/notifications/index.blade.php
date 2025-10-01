@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <div class="btn-group mb-3" role="group">
                    <a href="{{ route('notifications.index') }}"
                       class="btn btn-link @if(request('type') === null) font-weight-bold text-primary @endif">
                        Все
                    </a>

                    @foreach(\App\Models\Notification::getTypeLabels() as $type => $label)
                        <a href="{{ route('notifications.index', ['type' => $type]) }}"
                           class="btn btn-link @if(request('type') === $type) font-weight-bold text-primary @endif">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Заголовок</th>
                            <th scope="col">Текст</th>
                            <th scope="col">От</th>
                            <th scope="col">Заказ</th>
                            <th scope="col">Дата</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($notificationsUser as $notificationUser)
                            <tr>
                                <td>{{ $notificationUser->id }}</td>
                                <td>
                                    <a href="{{ route('notifications.show', $notificationUser->id) }}">
                                        {{ $notificationUser->title }}
                                        @if($notificationUser->read_at == null)
                                            <span class="badge badge-danger ml-3">Новое</span>
                                        @endif
                                    </a>
                                </td>
                                <td>{{ $notificationUser->body }}</td>
                                <td>{{ $notificationUser->sender->name }} {{ $notificationUser->sender->last_name }}</td>
                                <td>
                                    @if($notificationUser->order_id)
                                        <a href="{{ route('orders.show', $notificationUser->order_id) }}">
                                            {{ $notificationUser->order_id }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $notificationUser->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <x-pagination-component :collection="$notificationsUser" />

            </div>
        </div>
    </div>
@stop
