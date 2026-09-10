$(document).ready(function () {
    $('#validationCustomPhone').inputmask({ mask: '+7(999)999-99-99' });

    $('#bookingForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);

        // Сброс предыдущих ошибок
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').each(function () {
            $(this).data('default', $(this).data('default') || $(this).text());
            $(this).text($(this).data('default'));
        });
        $('#formAlert').addClass('d-none').text('');

        const payload = {
            first_name:     form.find('[name="first_name"]').val().trim(),
            last_name:      form.find('[name="last_name"]').val().trim(),
            phone:          form.find('[name="phone"]').val().trim(),
            email:          form.find('[name="email"]').val().trim(),
            check_in_date:  form.find('[name="check_in_date"]').val(),
            check_out_date: form.find('[name="check_out_date"]').val(),
            room_type_id:   form.find('[name="room_type_id"]').val()
        };

        $.ajax({
            url: 'api/bookings.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            dataType: 'json'
        })
        .done(function (res) {
            if (res.success) {
                form[0].reset();
                $('#formAlert')
                    .removeClass('d-none alert-danger')
                    .addClass('alert-success')
                    .text(res.message || 'Заявка успешно отправлена!');
            }
        })
        .fail(function (xhr) {
            const res = xhr.responseJSON || {};
            if (xhr.status === 422 && res.errors) {
                $.each(res.errors, function (field, message) {
                    const $input = form.find('[name="' + field + '"]');
                    $input.addClass('is-invalid');
                    $input.siblings('.invalid-feedback').text(message);
                });
                $('#formAlert').addClass('d-none');
            } else {
                $('#formAlert').removeClass('d-none').text(res.error || 'Ошибка отправки заявки');
            }
        });
    });
});
