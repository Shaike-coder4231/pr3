<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_db');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function db(): mysqli {
    static $m = null;
    if ($m === null) {
        $m = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($m->connect_error) { http_response_code(500); die('DB error'); }
        $m->set_charset('utf8mb4');
    }
    return $m;
}
