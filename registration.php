<?php
require_once __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $email === '' || $password === '') {
        $error = 'Заполните все поля.';
    } elseif (mb_strlen($login) > 15 || mb_strlen($email) > 50 || mb_strlen($password) > 20) {
        $error = 'Превышена допустимая длина поля.';
    } else {
        $sql = "
            SELECT id FROM users
            WHERE username = '$login' OR email = '$email'
            LIMIT 1
        ";

        $result = $mysqli->query($sql);
        $exists = $result ? $result->fetch_assoc() : null;

        if ($exists) {
            $error = 'Пользователь с таким логином или почтой уже существует.';
        } else {
            $sql = "
                INSERT INTO users (username, email, password)
                VALUES ('$login', '$email', '$password')
            ";

            $mysqli->query($sql);
            $userId = $mysqli->insert_id;
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $login;
            auth_cookie($userId, $login);

            header('Location: profile.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Регистрация</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <main class="page center-box">
    <section class="card center-content" style="width:min(420px,100%);">
      <h1 class="auth-heading">Регистрация</h1>

      <?php if ($error): ?>
        <div class="notice"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form class="form" action="" method="post">
        <div class="field">
          <label class="label" for="login">Логин</label>
          <input class="input" type="text" id="login" name="login" placeholder="Введите логин" maxlength="15" required>
        </div>

        <div class="field">
          <label class="label" for="email">Почта</label>
          <input class="input" type="email" id="email" name="email" placeholder="Введите почту" maxlength="50" required>
        </div>

        <div class="field">
          <label class="label" for="password">Пароль</label>
          <input class="input" type="password" id="password" name="password" placeholder="Введите пароль" maxlength="20" required>
        </div>

        <button class="btn" type="submit">Зарегистрироваться</button>
      </form>
    </section>
  </main>
</body>
</html>
