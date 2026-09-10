<?php
require_once __DIR__ . '/../helpers.php';

$action = $_GET['action'] ?? 'login';

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'Метод не поддерживается'], 405);

    $data = json_input();
    $login    = trim($data['login'] ?? '');
    $password = (string)($data['password'] ?? '');

    $errors = [];
    if ($login === '')    $errors['login']    = 'Введите логин';
    if ($password === '') $errors['password'] = 'Введите пароль';
    if ($errors) json_out(['errors' => $errors], 422);

    $stmt = db()->prepare('SELECT id, login, password_hash, role FROM users WHERE login = ? LIMIT 1');
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_out(['error' => 'Неверный логин или пароль'], 401);
    }

    session_regenerate_id(true);
    $_SESSION['user_id']    = (int)$user['id'];
    $_SESSION['user_role']  = $user['role'];
    $_SESSION['user_login'] = $user['login'];

    json_out([
        'success'  => true,
        'redirect' => $user['role'] === 'admin' ? 'admin.php' : 'index.php',
    ]);
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    json_out(['success' => true]);
}

json_out(['error' => 'Неизвестное действие'], 400);
