<?php
require_once __DIR__ . '/config.php';

function e(?string $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_out($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function is_logged_in(): bool { return !empty($_SESSION['user_id']); }
function is_admin(): bool { return is_logged_in() && ($_SESSION['user_role'] ?? '') === 'admin'; }

function require_admin(bool $api = false): void {
    if (is_admin()) return;
    if ($api) json_out(['error' => 'Требуется авторизация администратора'], 401);
    header('Location: login.php'); exit;
}

function json_input(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}
