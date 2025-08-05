$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownAutoWidth: true,
        placeholder: "Выберите тип работы",
        allowClear: true,
        width: '100%',
        language: "ru"
    });
});

$(document).on('select2:open', function () {
    document.querySelector('.select2-search__field').focus();
});
