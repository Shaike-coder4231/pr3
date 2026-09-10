$(document).ready(function () {
    Store.seed();

    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        $('#loginAlert').addClass('d-none').text('');

        const login    = form.find('#username').val().trim();
        const password = form.find('#password').val();

        if (!login)    { form.find('#username').addClass('is-invalid'); return; }
        if (!password) { form.find('#password').addClass('is-invalid'); return; }

        const session = Store.login(login, password);
        if (!session) {
            $('#loginAlert').removeClass('d-none').text('Неверный логин или пароль');
            return;
        }

        window.location.href = session.role === 'admin' ? 'admin.html' : 'index.html';
    });
});
