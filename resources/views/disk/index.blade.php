@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('disk.upload', ['folder' => $link]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <input type="file" class="form-control" id="file" name="files[]" required>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Загрузить файл</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('disk.create_folder', ['folder' => $link]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <input type="text" class="form-control" id="folder_name" name="folder_name" required>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Создать папку</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <input type="text" id="searchInput" class="form-control" placeholder="Поиск...">
            </div>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Имя</th>
                    <th scope="col">Дата изменения</th>
                    <th scope="col">Размер</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <a href="{{ route('disk.index', ['folder' => $backLink]) }}" class="active">
                            <i class="fas fa-reply text-dark mr-3"></i>...
                        </a>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                    @if(auth()->user()->role->name == 'admin' && $myFolderLink)
                    <tr>
                        <td>
                            <a href="{{ route('disk.index', ['folder' => $allFolders]) }}" class="active">
                                <i class="fas fa-folder-open text-dark mr-3"></i>Диски всех менеджеров
                            </a>
                        </td>
                    </tr>
                   @endif

                    @foreach($resources as $resource)
                        @if($resource['name'] == '\\')
                            @continue
                        @endif

                        <tr>
                            @if($resource['type'] == 'dir')
                                <td>
                                    <a href="{{ route('disk.index', ['folder' => $link.$resource['name']]) }}" class="active">
                                        <i class="far fa-folder-open text-warning mr-3"></i>{{ $resource['name'] }}
                                    </a>
                                </td>
                            @endif

                            @if($resource['type'] == 'file')
                                <td>
                                    <a href="{{ route('disk.index', ['folder' => $link.$resource['name'], 'is_file' => 1]) }}"
                                       target="_blank" class="active">
                                        @switch(pathinfo($resource['name'], PATHINFO_EXTENSION))
                                            @case('xls')
                                            @case('xlsx')
                                                <i class="far fa-file-excel text-success mr-3"></i>
                                                @break
                                            @case('doc')
                                            @case('docx')
                                                <i class="far fa-file-word text-info mr-3"></i>
                                                @break
                                            @case('jpg')
                                            @case('png')
                                            @case('jpeg')
                                            @case('gif')
                                                <i class="far fa-file-image text-primary mr-3"></i>
                                                @break
                                            @case('pdf')
                                                <i class="far fa-file-pdf text-danger mr-3"></i>
                                                @break
                                            @case('zip')
                                            @case('rar')
                                            @case('7z')
                                                <i class="far fa-archive text-dark mr-3"></i>
                                                @break
                                            @default
                                                <i class="far fa-file text-dark mr-3"></i>
                                                @break
                                        @endswitch
                                        {{ $resource['name'] }}
                                    </a>
                                </td>
                            @endif

                            @php
                                $lastModifiedDate = new DateTime($resource['modified']);
                                $lastModifiedDate->setTimezone(new DateTimeZone('Europe/Moscow'));
                            @endphp

                            <td>{{$lastModifiedDate->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($resource['type'] != 'dir')
                                    {{ round($resource['size'] / 1024) }} КБ
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('disk.delete', ['folder' => $link.$resource['name']]) }}"
                                   class="text-danger"
                                   onclick="return confirm('Вы действительно хотите удалить?');"
                                   title="Удалить"
                                >
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    // Проверяем каждую ячейку в каждой строке
                    if ($(this).find('td').length > 0 && !($(this).find('td:eq(0) a i.fas.fa-reply').length)) { // Исключаем первую строку с кнопкой возврата
                        return $(this).toggle(
                            $(this).text().toLowerCase().indexOf(value) >= 0
                        );
                    }
                });
            });
        });
    </script>
@endpush

@section('js')

@stop
