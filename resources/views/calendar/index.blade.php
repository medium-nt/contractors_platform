@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', $title)
@section('content_header_title', $title)

{{-- Content body: main page content --}}

@section('content_body')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Подключение FullCalendar -->
    <script src="{{ asset('vendor/fullcalendar/js/index.global.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ru',
                firstDay: 1,
                buttonText: {
                    today:    'Сегодня',
                    month:    'Месяц',
                    week:     'Неделя',
                    day:      'День',
                    list:     'Список'
                },
                headerToolbar: {
                    left: 'myCustomButton prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                customButtons: {
                    myCustomButton: {
                        text: 'Создать задачу',
                        click: function () {
                            window.location.href = '{{ route('tasks.create') }}';
                        }
                    }
                },
                views: {
                    timeGridWeek: {
                        titleFormat: { year: 'numeric', month: 'short', day: 'numeric' }
                    },
                    listWeek: {
                        buttonText: 'Список'
                    }
                },

                dayMaxEventRows: true,
                eventContent: function(arg) {
                    return {
                        html: `
                            <div class="badge bg-primary text-wrap"
                                style="display: block;
                                    width: 100%;
                                    white-space: normal;
                                    word-break: break-word;
                                    padding: 4px 6px;
                                    font-size: 1.0em;
                                    text-align: left;
                                "
                            >
                                ${arg.event.title}
                            </div>`
                    };
                },

                // Ограничение диапазона прокрутки
                validRange: function(nowDate) {
                    const start = new Date(nowDate.getFullYear(), nowDate.getMonth() - 1, 1);
                    const end = new Date(nowDate.getFullYear() + 1, nowDate.getMonth() + 1, 0);

                    return {
                        start: start,
                        end: end
                    };
                },

                events: @json($events),
            });
            calendar.render();
        });
    </script>
@stop
