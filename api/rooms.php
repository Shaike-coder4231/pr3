<?php
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_out(['error' => 'Метод не поддерживается'], 405);

$category = trim($_GET['category'] ?? '');

if ($category !== '') {
    $stmt = db()->prepare('SELECT id, name, price, image_url, features FROM room_types WHERE name = ? ORDER BY id');
    $stmt->bind_param('s', $category);
} else {
    $stmt = db()->prepare('SELECT id, name, price, image_url, features FROM room_types ORDER BY id');
}
$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $row['id']       = (int)$row['id'];
    $row['price']    = (float)$row['price'];
    $row['features'] = json_decode($row['features'], true) ?: [];
    $rooms[] = $row;
}

json_out(['rooms' => $rooms]);
