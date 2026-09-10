<?php
require_once __DIR__ . '/../helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

// --- СОЗДАНИЕ ЗАЯВКИ (гость) ---
if ($method === 'POST') {
    $data = json_input();

    $first_name     = trim($data['first_name'] ?? '');
    $last_name      = trim($data['last_name'] ?? '');
    $phone          = trim($data['phone'] ?? '');
    $email          = trim($data['email'] ?? '');
    $check_in_date  = trim($data['check_in_date'] ?? '');
    $check_out_date = trim($data['check_out_date'] ?? '');
    $room_type_id   = (int)($data['room_type_id'] ?? 0);

    $errors = [];

    if ($first_name === '' || mb_strlen($first_name) > 100)
        $errors['first_name'] = 'Пожалуйста, введите имя (до 100 символов)';

    if ($last_name === '' || mb_strlen($last_name) > 100)
        $errors['last_name'] = 'Пожалуйста, введите фамилию (до 100 символов)';

    if (!preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone))
        $errors['phone'] = 'Неверный формат телефона. Ожидается +7(999)999-99-99';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Некорректный email';

    $today    = new DateTime('today');
    $checkIn  = DateTime::createFromFormat('Y-m-d', $check_in_date);
    $checkOut = DateTime::createFromFormat('Y-m-d', $check_out_date);

    if (!$checkIn)                  $errors['check_in_date']  = 'Введите дату заезда';
    elseif ($checkIn < $today)      $errors['check_in_date']  = 'Дата заезда не может быть в прошлом';

    if (!$checkOut)                                            $errors['check_out_date'] = 'Введите дату выезда';
    elseif ($checkIn && $checkOut <= $checkIn)                 $errors['check_out_date'] = 'Дата выезда должна быть позже даты заезда';

    if ($room_type_id <= 0) {
        $errors['room_type_id'] = 'Не выбран номер';
    } else {
        $s = db()->prepare('SELECT id FROM room_types WHERE id = ? LIMIT 1');
        $s->bind_param('i', $room_type_id);
        $s->execute();
        if (!$s->get_result()->fetch_assoc()) $errors['room_type_id'] = 'Такого номера не существует';
    }

    if ($errors) json_out(['errors' => $errors], 422);

    $stmt = db()->prepare(
        'INSERT INTO bookings (room_type_id, first_name, last_name, phone, email, check_in_date, check_out_date, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, "pending")'
    );
    $stmt->bind_param('issssss',
        $room_type_id, $first_name, $last_name, $phone, $email, $check_in_date, $check_out_date
    );

    if (!$stmt->execute()) json_out(['error' => 'Не удалось сохранить заявку'], 500);

    json_out(['success' => true, 'message' => 'Заявка успешно отправлена!']);
}

// --- СПИСОК ЗАЯВОК (админ) ---
if ($method === 'GET') {
    require_admin(true);

    $stmt = db()->prepare(
        'SELECT b.id, b.first_name, b.last_name, b.phone, b.email,
                b.check_in_date, b.check_out_date, b.status,
                r.name AS room_name
         FROM bookings b
         JOIN room_types r ON r.id = b.room_type_id
         WHERE b.status = "pending"
         ORDER BY b.created_at DESC'
    );
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $items[] = $row;
    }
    json_out(['bookings' => $items]);
}

// --- ОБНОВЛЕНИЕ СТАТУСА (PUT) ---
if ($method === 'PUT') {
    require_admin(true);

    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) json_out(['error' => 'Не указан ID заявки'], 400);

    $data   = json_input();
    $status = $data['status'] ?? '';

    if (!in_array($status, ['approved', 'rejected'], true))
        json_out(['error' => 'Недопустимый статус'], 422);

    $stmt = db()->prepare('UPDATE bookings SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();

    json_out(['success' => true]);
}

// --- УДАЛЕНИЕ (DELETE) ---
if ($method === 'DELETE') {
    require_admin(true);

    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) json_out(['error' => 'Не указан ID заявки'], 400);

    $stmt = db()->prepare('DELETE FROM bookings WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) json_out(['error' => 'Заявка не найдена'], 404);

    json_out(['success' => true]);
}

json_out(['error' => 'Метод не поддерживается'], 405);
