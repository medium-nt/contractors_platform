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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Пример</h3>
        </div>
        <div class="card-body">
            <p>
                Тут какой-то текст
            </p>
        </div>
    </div>
@stop

@push('js')

@endpush

@push('css')

@endpush
