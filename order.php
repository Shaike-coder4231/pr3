<?php
require_once __DIR__ . '/helpers.php';

$roomTypeId = (int)($_GET['room_type_id'] ?? 0);
$roomType = null;

if ($roomTypeId > 0) {
    $s = db()->prepare('SELECT id, name, price FROM room_types WHERE id = ? LIMIT 1');
    $s->bind_param('i', $roomTypeId);
    $s->execute();
    $roomType = $s->get_result()->fetch_assoc();
}

$showSuccess = false;
$errors = [];

// Обработка POST (если JS отключён)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $check_in_date  = trim($_POST['check_in_date'] ?? '');
    $check_out_date = trim($_POST['check_out_date'] ?? '');
    $rid            = (int)($_POST['room_type_id'] ?? 0);

    if ($first_name === '')                                 $errors['first_name']     = 'Пожалуйста, введите имя';
    if ($last_name === '')                                  $errors['last_name']      = 'Пожалуйста, введите фамилию';
    if (!preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) $errors['phone']    = 'Неверный формат телефона';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))         $errors['email']          = 'Некорректный email';
    if ($check_in_date === '')                              $errors['check_in_date']  = 'Введите дату заезда';
    if ($check_out_date === '')                             $errors['check_out_date'] = 'Введите дату выезда';
    if ($check_in_date && $check_out_date && $check_out_date <= $check_in_date)
        $errors['check_out_date'] = 'Дата выезда должна быть позже даты заезда';
    if ($rid <= 0)                                          $errors['room_type_id']   = 'Не выбран номер';

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO bookings (room_type_id, first_name, last_name, phone, email, check_in_date, check_out_date, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, "pending")'
        );
        $stmt->bind_param('issssss', $rid, $first_name, $last_name, $phone, $email, $check_in_date, $check_out_date);
        if ($stmt->execute()) {
            $showSuccess = true;
        } else {
            $errors['general'] = 'Не удалось сохранить заявку';
        }
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Светлые Сны — Бронирование</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body class="container">
<header class="d-flex flex-wrap justify-content-center py-3">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <span class="fs-4 mx-2 fw-medium">Светлые Сны</span>
    </a>
    <ul class="nav align-items-center">
        <li class="nav-item"><a href="#" class="nav-link">Приезжайте как гости, уезжайте как друзья!</a></li>
        <?php if (is_logged_in()): ?>
            <li class="nav-item"><a href="logout.php" class="nav-link">Выход (<?= e($_SESSION['user_login']) ?>)</a></li>
        <?php else: ?>
            <li class="nav-item"><a href="login.php" class="nav-link">Вход</a></li>
        <?php endif; ?>
    </ul>
</header>

<?php if ($showSuccess): ?>
    <div class="alert alert-success text-center" role="alert">Заявка успешно отправлена! Ожидайте подтверждения.</div>
<?php endif; ?>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger text-center" role="alert"><?= e($errors['general']) ?></div>
<?php endif; ?>

<main>
    <div class="d-flex justify-content-between flex-wrap align-items-center">
        <h1>Бронирование номера</h1>
        <?php if ($roomType): ?>
            <span class="badge text-bg-success fs-6">
                <?= e($roomType['name']) ?> — <?= number_format((float)$roomType['price'], 0, '.', ' ') ?> ₽/чел
            </span>
        <?php endif; ?>
    </div>

    <div id="formAlert" class="alert alert-danger d-none" role="alert"></div>

    <form class="row g-3 my-2" id="bookingForm" method="post" novalidate>
        <input type="hidden" name="room_type_id" id="room_type_id" value="<?= (int)$roomTypeId ?>">

        <div class="col-md-4">
            <label for="first_name" class="form-label">Имя</label>
            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                   id="first_name" name="first_name" value="<?= e($_POST['first_name'] ?? '') ?>" required>
            <div class="invalid-feedback"><?= e($errors['first_name'] ?? 'Пожалуйста, введите имя') ?></div>
        </div>
        <div class="col-md-4">
            <label for="last_name" class="form-label">Фамилия</label>
            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                   id="last_name" name="last_name" value="<?= e($_POST['last_name'] ?? '') ?>" required>
            <div class="invalid-feedback"><?= e($errors['last_name'] ?? 'Пожалуйста, введите фамилию') ?></div>
        </div>
        <div class="col-md-4">
            <label for="validationCustomPhone" class="form-label">Телефон</label>
            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                   id="validationCustomPhone" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" required>
            <div class="invalid-feedback"><?= e($errors['phone'] ?? 'Пожалуйста, введите номер телефона') ?></div>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Почта</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
            <div class="invalid-feedback"><?= e($errors['email'] ?? 'Пожалуйста, введите email') ?></div>
        </div>
        <div class="col-md-3">
            <label for="check_in_date" class="form-label">Дата заезда</label>
            <input type="date" class="form-control <?= isset($errors['check_in_date']) ? 'is-invalid' : '' ?>"
                   id="check_in_date" name="check_in_date" value="<?= e($_POST['check_in_date'] ?? '') ?>" required>
            <div class="invalid-feedback"><?= e($errors['check_in_date'] ?? 'Пожалуйста, введите дату заезда') ?></div>
        </div>
        <div class="col-md-3">
            <label for="check_out_date" class="form-label">Дата выезда</label>
            <input type="date" class="form-control <?= isset($errors['check_out_date']) ? 'is-invalid' : '' ?>"
                   id="check_out_date" name="check_out_date" value="<?= e($_POST['check_out_date'] ?? '') ?>" required>
            <div class="invalid-feedback"><?= e($errors['check_out_date'] ?? 'Пожалуйста, введите дату выезда') ?></div>
        </div>
        <div class="d-grid gap-2">
            <button class="btn btn-success" type="submit">Отправить заявку</button>
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
<script src="js/jquery.inputmask.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/order.js"></script>
</body>
</html>
