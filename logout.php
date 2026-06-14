<?php
require_once __DIR__ . '/db.php';

$_SESSION = [];
session_destroy();

setcookie('user_id', '', time() - 3600, '/');
setcookie('username', '', time() - 3600, '/');

header('Location: index.php');
exit;
?>
