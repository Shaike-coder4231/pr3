$(document).ready(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        $('#loginAlert').addClass('d-none').text('');

        const payload = {
            login:    form.find('#username').val().trim(),
            password: form.find('#password').val()
        };

        $.ajax({
            url: 'api/auth.php?action=login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            dataType: 'json'
        })
        .done(function (res) {
            if (res.success && res.redirect) {
                window.location.href = res.redirect;
            }
        })
        .fail(function (xhr) {
            const res = xhr.responseJSON || {};
            if (xhr.status === 422 && res.errors) {
                $.each(res.errors, function (field, message) {
                    const map = { login: '#username', password: '#password' };
                    const $input = form.find(map[field] || ('[name="' + field + '"]'));
                    $input.addClass('is-invalid');
                    $input.siblings('.invalid-feedback').text(message);
                });
            } else {
                $('#loginAlert').removeClass('d-none').text(res.error || 'Ошибка входа');
            }
        });
    });
});
