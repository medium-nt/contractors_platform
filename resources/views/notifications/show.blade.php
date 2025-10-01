@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">

                    <a href="{{ route('notifications.index') }}"
                       class="btn btn-outline-secondary mr-3 mb-3">
                        <i class="fas fa-arrow-left mr-1"></i> Назад
                    </a>

                    <div class="form-group">
                        <label for="body">Текст</label>
                        <textarea class="form-control" rows="10"
                                  readonly>{{ $notification->body }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
