$(document).ready(function () {
    Store.seed();

    $('#validationCustomPhone').inputmask({ mask: '+7(999)999-99-99' });

    // Подставляем номер из URL
    const params = new URLSearchParams(window.location.search);
    const roomId = params.get('room_type_id');
    const room   = roomId ? Store.getRoomById(roomId) : null;

    if (room) {
        $('#room_type_id').val(room.id);
        $('#roomBadge')
            .removeClass('d-none')
            .text(`${room.name} — ${Number(room.price).toLocaleString('ru-RU')} ₽/чел`);
    }

    $('#bookingForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);

        // Сброс ошибок
        form.find('.is-invalid').removeClass('is-invalid');
        $('#formAlert').addClass('d-none').text('');

        const payload = {
            first_name:     form.find('[name="first_name"]').val().trim(),
            last_name:      form.find('[name="last_name"]').val().trim(),
            phone:          form.find('[name="phone"]').val().trim(),
            email:          form.find('[name="email"]').val().trim(),
            check_in_date:  form.find('[name="check_in_date"]').val(),
            check_out_date: form.find('[name="check_out_date"]').val(),
            room_type_id:   Number(form.find('[name="room_type_id"]').val())
        };

        const errors = validateBooking(payload);

        if (Object.keys(errors).length) {
            $.each(errors, function (field, message) {
                const $input = form.find('[name="' + field + '"]');
                $input.addClass('is-invalid');
                $input.siblings('.invalid-feedback').text(message);
            });
            return;
        }

        // Сохраняем в localStorage (эмуляция POST на сервер)
        Store.addBooking(payload);

        form[0].reset();
        $('#room_type_id').val(payload.room_type_id);
        $('#formAlert')
            .removeClass('d-none alert-danger')
            .addClass('alert-success')
            .text('Заявка успешно отправлена! Ожидайте подтверждения.');
    });

    // --- Валидация (та же логика, что была на сервере) ---
    function validateBooking(d) {
        const errors = {};

        if (!d.first_name)                       errors.first_name = 'Пожалуйста, введите имя';
        else if (d.first_name.length > 100)      errors.first_name = 'Имя слишком длинное';

        if (!d.last_name)                        errors.last_name  = 'Пожалуйста, введите фамилию';
        else if (d.last_name.length > 100)       errors.last_name  = 'Фамилия слишком длинная';

        if (!/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/.test(d.phone))
            errors.phone = 'Неверный формат телефона. Ожидается +7(999)999-99-99';

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(d.email))
            errors.email = 'Некорректный email';

        const today     = new Date(); today.setHours(0,0,0,0);
        const checkIn   = d.check_in_date  ? new Date(d.check_in_date)  : null;
        const checkOut  = d.check_out_date ? new Date(d.check_out_date) : null;

        if (!checkIn)                            errors.check_in_date  = 'Введите дату заезда';
        else if (checkIn < today)                errors.check_in_date  = 'Дата заезда не может быть в прошлом';

        if (!checkOut)                           errors.check_out_date = 'Введите дату выезда';
        else if (checkIn && checkOut <= checkIn) errors.check_out_date = 'Дата выезда должна быть позже даты заезда';

        if (!d.room_type_id)                     errors.room_type_id   = 'Не выбран номер';

        return errors;
    }
});
