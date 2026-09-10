<?php
require_once __DIR__ . '/helpers.php';
require_admin();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Светлые Сны — Панель администратора</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="container">
<header class="d-flex flex-wrap justify-content-center py-3">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <span class="fs-4 mx-2 fw-medium">Светлые Сны</span>
    </a>
    <ul class="nav align-items-center">
        <li class="nav-item"><a href="index.php" class="nav-link">Каталог</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link">Выход (<?= e($_SESSION['user_login']) ?>)</a></li>
    </ul>
</header>

<main>
    <h1>Панель администратора</h1>
    <p class="text-body-secondary">Заявки на бронирование, ожидающие модерации.</p>

    <div id="bookingsAlert" class="alert d-none" role="alert"></div>
    <div id="bookingsContainer" class="d-flex justify-content-around flex-wrap align-items-center"></div>
    <div id="bookingsEmpty" class="alert alert-info mt-3 d-none">Нет заявок на модерации.</div>
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
<script src="js/admin.js"></script>
</body>
</html>
