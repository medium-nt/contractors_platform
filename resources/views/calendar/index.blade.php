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

                <style>
                    /* Общие стили для модального окна */
                    .modal-content {
                        border-radius: 10px;
                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
                        border: none;
                        background-color: #fdfdfd;
                    }

                    .modal-header {
                        background-color: #007bff;
                        color: white;
                        border-top-left-radius: 10px;
                        border-top-right-radius: 10px;
                        padding: 1rem 1.25rem;
                    }

                    .modal-title {
                        font-weight: 600;
                        font-size: 1.2rem;
                    }

                    .btn-close {
                        background: none;
                        border: none;
                        color: white;
                        font-size: 1.2rem;
                    }

                    .modal-body {
                        padding: 1rem 1.5rem;
                        font-size: 0.95rem;
                        color: #333;
                    }

                    .modal-body p {
                        margin-bottom: 0.75rem;
                    }

                    .modal-body strong {
                        color: #555;
                    }

                    .modal-footer {
                        background-color: #f1f1f1;
                        border-bottom-left-radius: 10px;
                        border-bottom-right-radius: 10px;
                        padding: 0.75rem 1.25rem;
                        display: flex;
                        justify-content: space-between;
                    }

                    .btn-primary.btn-sm {
                        background-color: #007bff;
                        border-color: #007bff;
                        font-weight: 500;
                        padding: 0.375rem 0.75rem;
                    }

                    .btn-secondary.btn-sm {
                        background-color: #6c757d;
                        border-color: #6c757d;
                        font-weight: 500;
                        padding: 0.375rem 0.75rem;
                    }

                    /* Адаптивность */
                    @media (max-width: 576px) {
                        .modal-dialog {
                            margin: 1rem auto;
                        }
                    }
                </style>

                <!-- Модальное окно -->
                <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="eventModalLabel"><span data-title="true"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Закрыть">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Название:</strong> <span data-title="true"></span></p>
                                <p><strong>Описание:</strong> <span id="eventDescription"></span></p>
                                <p><strong>Срок:</strong> <span id="eventStart"></span></p>
                            </div>
                            <div class="modal-footer">
                                <a href="" class="btn btn-primary btn-sm" id="eventUrl">Перейти к задаче</a>
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Закрыть</button>
                            </div>
                        </div>
                    </div>
                </div>

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
                    let badgeClass = 'bg-primary';
                    if (arg.event.extendedProps.completed_at) {
                        badgeClass = 'bg-secondary';
                    }

                    return {
                        html: `
                            <div class="badge ${badgeClass} text-wrap"
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

                eventClick: function (info) {
                    // Останавливаем переход по ссылке
                    info.jsEvent.preventDefault();

                    // Заполняем модальное окно
                    document.querySelectorAll('[data-title="true"]').forEach(el => {
                        el.textContent = info.event.title;
                    });
                    document.getElementById('eventDescription').textContent = info.event.extendedProps.description;
                    document.getElementById('eventStart').textContent = info.event.start.toLocaleString();
                    document.getElementById('eventUrl').href = info.event.url;

                    // Показываем модальное окно
                    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                },

                // Ограничение диапазона прокрутки
                validRange: function(nowDate) {
                    const start = new Date(2025, 5, 1);
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
