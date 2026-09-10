$(document).ready(function () {
    Store.seed();

    // Пускаем только админа
    if (!Store.isAdmin()) {
        window.location.href = 'login.html';
        return;
    }

    const container = $('#bookingsContainer');
    const empty     = $('#bookingsEmpty');
    const alertBox  = $('#bookingsAlert');

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }

    function showAlert(type, text) {
        alertBox
            .removeClass('d-none alert-success alert-danger')
            .addClass('alert-' + type)
            .text(text);
    }

    function renderBooking(b) {
        return `
            <div class="card" data-id="${b.id}">
                <div class="card-body">
                    <h5>Фамилия: ${escapeHtml(b.last_name)}</h5>
                    <h5>Имя: ${escapeHtml(b.first_name)}</h5>
                    <h5>Телефон: ${escapeHtml(b.phone)}</h5>
                    <h5>Email: ${escapeHtml(b.email)}</h5>
                    <h5>Номер: ${escapeHtml(b.room_name)}</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Дата заезда: ${escapeHtml(b.check_in_date)}</li>
                        <li class="list-group-item">Дата выезда: ${escapeHtml(b.check_out_date)}</li>
                    </ul>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-success" data-action="approve">Одобрить</button>
                    <button class="btn btn-danger"  data-action="delete">Удалить</button>
                </div>
            </div>
        `;
    }

    function loadBookings() {
        const items = Store.getBookings('pending');
        container.empty();

        if (!items.length) {
            empty.removeClass('d-none');
            return;
        }
        empty.addClass('d-none');
        items.forEach(b => container.append(renderBooking(b)));
    }

    container.on('click', 'button[data-action]', function () {
        const $card  = $(this).closest('.card');
        const id     = $card.data('id');
        const action = $(this).data('action');

        if (action === 'approve') {
            Store.updateBookingStatus(id, 'approved');
            $card.fadeOut(200, function () { $(this).remove(); checkEmpty(); });
            showAlert('success', 'Заявка одобрена');
        }

        if (action === 'delete') {
            Store.deleteBooking(id);
            $card.fadeOut(200, function () { $(this).remove(); checkEmpty(); });
            showAlert('success', 'Заявка удалена');
        }
    });

    function checkEmpty() {
        if (container.children('.card').length === 0) empty.removeClass('d-none');
    }

    // Кнопка «Выход»
    $(document).on('click', '#logoutLink', function (e) {
        e.preventDefault();
        Store.logout();
        window.location.href = 'index.html';
    });

    loadBookings();
});
