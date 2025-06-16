@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('setting.save') }}" method="POST">
                            @method('POST')
                            @csrf
                            {{--
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label for="title">xxx</label>
                                    <select name="xxx" id="xxx" class="form-control">
                                        <option value="1" {{ $settings->xxx == 1 ? 'selected' : '' }}>Да</option>
                                        <option value="0" {{ $settings->xxx == 0 ? 'selected' : '' }}>Нет</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Сохранить</button>
                            --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

