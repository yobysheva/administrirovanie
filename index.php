<?php
require_once __DIR__ . '/db.php';

$userId = current_user_id();
$username = current_username($mysqli);

$posts = [];
if ($userId > 0) {
    $sql = "
        SELECT p.id, p.title, p.content, p.image_path, p.created_at, u.username
        FROM posts p
        INNER JOIN users u ON u.id = p.user_id
        WHERE p.user_id = $userId
        ORDER BY p.created_at DESC
    ";

    $result = $mysqli->query($sql);

    if (!$result) {
        die('Ошибка SQL: ' . $mysqli->error);
    }

    $posts = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Главная</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <main class="page center-box">
    <?php if (!$userId): ?>
      <section class="card center-content">
        <h1 class="small-title">Войдите</h1>
        <div class="btn-row">
          <a class="btn btn-outline" href="registration.php">Регистрация</a>
          <a class="btn" href="login.php">Логин</a>
        </div>
      </section>
    <?php else: ?>
      <section style="width:min(760px,100%);">
        <div class="card navbar">
          <div class="navbar-title"><?= $username ?></div>
          <nav class="nav-links">
            <a class="nav-link active" href="index.php">Главная</a>
            <a class="nav-link" href="profile.php">Профиль</a>
            <a class="nav-link" href="posts.php">Посты</a>
            <a class="nav-link" href="logout.php">Выход</a>
          </nav>
        </div>

        <div class="card post-card">
          <h2 class="post-title">Ваши посты</h2>

          <?php if (!$posts): ?>
            <div class="notice">Постов пока нет. Создайте первый пост в профиле.</div>
          <?php else: ?>
            <div class="post-list">
              <?php foreach ($posts as $post): ?>
                <a href="posts.php?id=<?= (int)$post['id'] ?>">
                <article class="post-item">
                  <h3 class="post-title" style="font-size:20px; margin-bottom:8px;"><?= $post['title'] ?></h3>
                  <div class="post-meta">
                    <?= $post['username'] ?> · <?= $post['created_at'] ?>
                  </div>
                  <div class="post-text"><?= nl2br($post['content']) ?></div>
                  <?php if (!empty($post['image_path'])): ?>
                    <div class="post-image">
                      <img src="<?= $post['image_path'] ?>" alt="Изображение поста">
                    </div>
                  <?php endif; ?>
                </article>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
