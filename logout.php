<?php
require_once __DIR__ . '/helpers.php';
$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;
