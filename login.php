<?php
require_once __DIR__ . '/helpers.php';
if (is_logged_in()) {
    header('Location: ' . (is_admin() ? 'admin.php' : 'index.php'));
    exit;
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Светлые Сны — Вход</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="container">
<header class="d-flex flex-wrap justify-content-center py-3">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <span class="fs-4 mx-2 fw-medium">Светлые Сны</span>
    </a>
    <ul class="nav">
        <li class="nav-item"><a href="#" class="nav-link">Приезжайте как гости, уезжайте как друзья!</a></li>
    </ul>
</header>

<main>
    <h1>Вход</h1>

    <div id="loginAlert" class="alert alert-danger d-none" role="alert"></div>

    <form class="my-2" id="loginForm" novalidate>
        <div class="my-2">
            <label for="username" class="form-label">Логин</label>
            <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
            <div class="invalid-feedback">Введите логин</div>
        </div>
        <div class="my-2">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            <div class="invalid-feedback">Введите пароль</div>
        </div>
        <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Войти</button>
        </div>
    </form>
</main>

<footer class="py-2 my-2">
    <ul class="nav justify-content-between align-items-center">
        <li class="nav-item"><a href="#" class="nav-link text-body-secondary">ул. г.Москва, ул. Ивовая, 48</a></li>
        <li class="nav-item"><a href="#" class="nav-link text-body-secondary">Время работы: Пн-Пт, с 8:00-17:00</a></li>
        <li class="nav-item"><a href="tel:88005553535" class="nav-link text-body-secondary">тел. 8 (800) 555-35-35</a></li>
        <li class="nav-item"><a href="mailto:info@svetlye-sny.ru" class="nav-link text-body-secondary">Email: info@svetlye-sny.ru</a></li>
    </ul>
</footer>

<script src="js/jquery-3.7.1.slim.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/login.js"></script>
</body>
</html>
