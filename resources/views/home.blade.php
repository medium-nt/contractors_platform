@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Пример</h3>
        </div>
        <div class="card-body">
            Тут какой-то текст
        </div>
    </div>
@stop

@push('js')

@endpush

@push('css')

@endpush
