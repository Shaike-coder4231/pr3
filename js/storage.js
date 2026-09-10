/**
 * Аналог БД на localStorage.
 * Хранит: room_types, bookings, users, текущую сессию.
 */
(function (window) {
    'use strict';

    const KEYS = {
        rooms:    'hotel_room_types',
        bookings: 'hotel_bookings',
        users:    'hotel_users',
        session:  'hotel_session',
        seeded:   'hotel_seeded'
    };

    // ---------- Начальные данные ----------
    const DEFAULT_ROOMS = [
        {
            id: 1,
            name: 'Стандарт',
            price: 10000,
            image_url: 'img/standart.png',
            features: ['Включен завтрак', 'Душ + Ванна']
        },
        {
            id: 2,
            name: 'Студия',
            price: 8000,
            image_url: 'img/studio.jpg',
            features: ['Включен завтрак, обед', 'Душ + Ванна', 'Кондиционер']
        },
        {
            id: 3,
            name: 'Люкс',
            price: 19000,
            image_url: 'img/lux.png',
            features: ['Включен завтрак, обед, ужин', 'Душ + Ванна', 'Кондиционер', 'Телевизор', 'Мини-бар', 'Вид на город']
        }
    ];

    // Демо-админ: логин admin / пароль admin123
    // Хеш здесь не нужен — это статическая версия без реального бэкенда.
    const DEFAULT_USERS = [
        { id: 1, login: 'admin', password: 'admin123', role: 'admin' }
    ];

    // ---------- Утилиты ----------
    function read(key, fallback) {
        try {
            const raw = localStorage.getItem(key);
            return raw ? JSON.parse(raw) : fallback;
        } catch (e) {
            return fallback;
        }
    }

    function write(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }

    // ---------- Инициализация (seed) ----------
    function seed() {
        if (!localStorage.getItem(KEYS.seeded)) {
            write(KEYS.rooms, DEFAULT_ROOMS);
            write(KEYS.users, DEFAULT_USERS);
            write(KEYS.bookings, []);
            localStorage.setItem(KEYS.seeded, '1');
        }
    }

    // ---------- Публичное API ----------
    const Store = {
        seed,

        // --- Номера ---
        getRooms(filterName) {
            const rooms = read(KEYS.rooms, []);
            if (!filterName) return rooms;
            return rooms.filter(r => r.name === filterName);
        },
        getRoomById(id) {
            return read(KEYS.rooms, []).find(r => r.id === Number(id)) || null;
        },

        // --- Заявки ---
        getBookings(status) {
            const all = read(KEYS.bookings, []);
            const withRoom = all.map(b => {
                const room = this.getRoomById(b.room_type_id);
                return { ...b, room_name: room ? room.name : '—' };
            });
            if (!status) return withRoom;
            return withRoom.filter(b => b.status === status);
        },
        addBooking(booking) {
            const all = read(KEYS.bookings, []);
            const nextId = all.length ? Math.max(...all.map(b => b.id)) + 1 : 1;
            const item = {
                id: nextId,
                room_type_id: booking.room_type_id,
                first_name: booking.first_name,
                last_name: booking.last_name,
                phone: booking.phone,
                email: booking.email,
                check_in_date: booking.check_in_date,
                check_out_date: booking.check_out_date,
                status: 'pending',
                created_at: new Date().toISOString()
            };
            all.push(item);
            write(KEYS.bookings, all);
            return item;
        },
        updateBookingStatus(id, status) {
            const all = read(KEYS.bookings, []);
            const idx = all.findIndex(b => b.id === Number(id));
            if (idx === -1) return false;
            all[idx].status = status;
            write(KEYS.bookings, all);
            return true;
        },
        deleteBooking(id) {
            const all = read(KEYS.bookings, []);
            const filtered = all.filter(b => b.id !== Number(id));
            write(KEYS.bookings, filtered);
            return filtered.length !== all.length;
        },

        // --- Пользователи / сессия ---
        login(login, password) {
            const users = read(KEYS.users, []);
            const user = users.find(u => u.login === login && u.password === password);
            if (!user) return null;
            const session = { id: user.id, login: user.login, role: user.role };
            write(KEYS.session, session);
            return session;
        },
        logout() {
            localStorage.removeItem(KEYS.session);
        },
        getSession() {
            return read(KEYS.session, null);
        },
        isAdmin() {
            const s = this.getSession();
            return !!s && s.role === 'admin';
        }
    };

    window.Store = Store;
})(window);
