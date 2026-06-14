<?php
declare(strict_types=1);

session_start();

$DB_HOST = 'db';
$DB_PORT = 3306;
$DB_USER = 'lab_user';
$DB_PASS = 'lab_password';
$DB_NAME = 'lab_web';

$mysqli = null;
$lastError = '';

for ($attempt = 1; $attempt <= 15; $attempt++) {
    try {
        $mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
        if (!$mysqli->connect_error) {
            break;
        }
        $lastError = $mysqli->connect_error;
    } catch (Throwable $e) {
        $lastError = $e->getMessage();
    }
    usleep(300000);
}

if (!$mysqli || $mysqli->connect_error) {
    http_response_code(500);
    die('Ошибка подключения к базе данных: ' . $lastError);
}

$mysqli->set_charset('utf8mb4');

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']) || !empty($_COOKIE['user_id']);
}

function current_user_id(): int
{
    if (!empty($_SESSION['user_id'])) {
        return (int)$_SESSION['user_id'];
    }
    if (!empty($_COOKIE['user_id'])) {
        return (int)$_COOKIE['user_id'];
    }
    return 0;
}

function current_username(mysqli $mysqli): string
{
    if (!empty($_SESSION['username'])) {
        return (string)$_SESSION['username'];
    }
    if (!empty($_COOKIE['username'])) {
        return (string)$_COOKIE['username'];
    }

    $userId = current_user_id();
    if ($userId <= 0) {
        return '';
    }

    // $stmt = $mysqli->query('SELECT username FROM users WHERE id = $userId');
    // $stmt->execute();
    // $result = $stmt->get_result();
    // $row = $result->fetch_assoc();
    // return $row['username'] ?? '';

    $sql = "
        SELECT username FROM users WHERE id = '$userId'
    ";

    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    return $row['username'] ?? '';
}

function auth_cookie(int $userId, string $username): void
{
    $expires = time() + 60 * 60 * 24 * 30;
    $options = [
        'expires' => $expires,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    setcookie('user_id', (string)$userId, $options);
    setcookie('username', $username, $options);
}
?>