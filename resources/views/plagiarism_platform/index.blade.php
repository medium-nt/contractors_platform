@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <a href="{{ route('plagiarism_platforms.create') }}" class="btn btn-primary mr-3 mb-3">Добавить платформу</a>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название</th>
                            <th scope="col">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($plagiarismPlatforms as $platform)
                            <tr>
                                <td>{{ $platform->id }}</td>
                                <td>{{ $platform->title }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('plagiarism_platforms.edit', ['plagiarism_platform' => $platform->id]) }}"
                                           title="Редактировать"
                                           class="btn btn-primary mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('plagiarism_platforms.destroy', ['plagiarism_platform' => $platform->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mr-1"
                                                    onclick="return confirm('Вы уверены что хотите удалить данную платформу?')"
                                                    title="Удалить">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <x-pagination-component :collection="$plagiarismPlatforms" />

            </div>
        </div>
    </div>
@stop
