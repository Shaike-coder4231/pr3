$(document).ready(function () {
    Store.seed();

    const container = $('#roomsContainer');
    const empty     = $('#roomsEmpty');

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }

    function renderRoom(room) {
        const features = (room.features || [])
            .map(f => `<li class="list-group-item">${escapeHtml(f)}</li>`)
            .join('');

        return `
            <div class="card">
                <img src="${escapeHtml(room.image_url)}" class="card-img-top" alt="${escapeHtml(room.name)}">
                <div class="card-body">
                    <h3>Категория: ${escapeHtml(room.name)}</h3>
                    <h5>Цена: ${Number(room.price).toLocaleString('ru-RU')} ₽ / чел</h5>
                    <h5>Характеристики:</h5>
                    <ul class="list-group">${features}</ul>
                </div>
                <div class="d-grid gap-2">
                    <a href="order.html?room_type_id=${room.id}" class="btn btn-success">Забронировать</a>
                </div>
            </div>
        `;
    }

    function loadRooms(category) {
        const rooms = Store.getRooms(category || '');
        container.empty();

        if (!rooms.length) {
            empty.removeClass('d-none');
            return;
        }
        empty.addClass('d-none');
        rooms.forEach(r => container.append(renderRoom(r)));
    }

    $('#applyFilter').on('click', function () {
        loadRooms($('#categoryFilter').val());
    });

    $('#resetFilter').on('click', function () {
        $('#categoryFilter').val('');
        loadRooms('');
    });

    // Показать ссылку на админку / вход/выход в шапке
    if (Store.isAdmin()) {
        $('header .nav').append('<li class="nav-item"><a href="admin.html" class="nav-link">Админ-панель</a></li>');
    }

    const session = Store.getSession();
    if (session) {
        $('header .nav').append(
            `<li class="nav-item"><a href="#" id="logoutLink" class="nav-link">Выход (${escapeHtml(session.login)})</a></li>`
        );
        $(document).on('click', '#logoutLink', function (e) {
            e.preventDefault();
            Store.logout();
            window.location.href = 'index.html';
        });
    } else {
        $('header .nav').append('<li class="nav-item"><a href="login.html" class="nav-link">Вход</a></li>');
    }

    loadRooms('');
});
