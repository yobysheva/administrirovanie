<?php
require_once __DIR__ . '/db.php';

$username = current_username($mysqli);

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$username = current_username($mysqli);
$postId = isset($_GET['id']) ? $_GET['id'] : 0;
$post = null;

if ($postId > 0) {
    $sql = "
        SELECT *
        FROM posts
        WHERE id = $postId
    ";
    $result = $mysqli->query($sql);
    $post = $result->fetch_assoc();
} else {
    $sql = "
        SELECT p.id, p.title, p.content, p.image_path, p.created_at, u.username
        FROM posts p
        INNER JOIN users u ON u.id = p.user_id
        ORDER BY p.created_at DESC
    ";
    $result = $mysqli->query($sql);
    $posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Посты</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <main class="page">
    <header class="navbar card">
      <div class="navbar-title"><?= $username ?></div>
      <nav class="nav-links">
        <a class="nav-link" href="index.php">Главная</a>
        <a class="nav-link" href="profile.php">Профиль</a>
        <a class="nav-link active" href="posts.php">Посты</a>
        <a class="nav-link" href="logout.php">Выход</a>
      </nav>
    </header>
<?php if ($postId > 0): ?>
      <?php if (!$post): ?>
        <section class="card post-card">
          <h1 class="post-title">Пост не найден</h1>
          <a class="btn btn-outline" href="posts.php">Назад к постам</a>
        </section>
      <?php else: ?>
        <section class="card post-card">
          <h1 class="post-title"><?= $post['title'] ?></h1>
          <div class="post-meta">
            <?= $post['user_id'] ?> · <?= $post['created_at'] ?>
          </div>
          <?php if (!empty($post['image_path'])): ?>
            <div class="post-image">
              <img src="<?= $post['image_path'] ?>" alt="Изображение поста">
            </div>
          <?php endif; ?>
          <div class="post-text">
            <?= $post['content'] ?>
          </div>
          <a class="btn btn-outline" href="posts.php">Назад к списку</a>
        </section>
      <?php endif; ?>
    <?php else: ?>
    <section class="card post-card">
      <?php if (!$posts): ?>
        <div class="notice">Постов пока нет.</div>
      <?php else: ?>
        <div class="post-list">
          <?php foreach ($posts as $post): ?>
             <a class="post-link" href="posts.php?id=<?= (int)$post['id'] ?>">
            <article class="post-item">
              <h3 class="post-title" style="font-size:20px; margin-bottom:8px;"><?= $post['title']?></h3>
              <div class="post-meta"><?= $post['username'] ?> · <?= $post['created_at'] ?></div>
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
    </section>
    <?php endif; ?>
  </main>
</body>
</html>
