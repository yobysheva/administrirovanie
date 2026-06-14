<?php
require_once __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $sql = "
        SELECT id, username, password
        FROM users
        WHERE username = '$login'
          AND password = '$password'
        LIMIT 1
    ";

    $result = $mysqli->query($sql);
    $user = $result ? $result->fetch_assoc() : null;

    if ($user) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        auth_cookie((int)$user['id'], $user['username']);
        header('Location: profile.php');
        exit;
    }

    $error = 'Неверный логин или пароль.';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <main class="page center-box">
    <section class="card center-content" style="width:min(420px,100%);">
      <h1 class="auth-heading">Вход</h1>

      <?php if ($error): ?>
        <div class="notice"><?= $error ?></div>
      <?php endif; ?>

      <form class="form" action="" method="post">
        <div class="field">
          <label class="label" for="login">Логин</label>
          <input class="input" type="text" id="login" name="login" placeholder="Введите логин" required>
        </div>

        <div class="field">
          <label class="label" for="password">Пароль</label>
          <input class="input" type="password" id="password" name="password" placeholder="Введите пароль" required>
        </div>

        <button class="btn" type="submit">Войти</button>
      </form>
    </section>
  </main>
</body>
</html>
