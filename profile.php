<?php
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$userId = current_user_id();
$username = current_username($mysqli);

$notice = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_post'])) {
    $profileText = trim($_POST['profile_text'] ?? '');
    $title = trim($_POST['post_title'] ?? '');
    $content = trim($_POST['post_body'] ?? '');

    $imagePath = '';
    if (!empty($_FILES['post_file']['name']) && is_uploaded_file($_FILES['post_file']['tmp_name'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = mime_content_type($_FILES['post_file']['tmp_name']);
        if (in_array($mime, $allowed, true)) {
            $ext = pathinfo($_FILES['post_file']['name'], PATHINFO_EXTENSION);
            $name = 'post_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
            $target = __DIR__ . '/uploads/' . $name;
            if (move_uploaded_file($_FILES['post_file']['tmp_name'], $target)) {
                $imagePath = 'uploads/' . $name;
            }
        }
    }

    if ($title === '' || $content === '') {
        $error = 'Введите название и текст поста.';
    } else {
        $sql = "
            INSERT INTO posts (user_id, title, content, image_path)
            VALUES ($userId, '$title', '$content', '$imagePath')
        ";

        $mysqli->query($sql);
        $notice = 'Пост сохранён. Картинка лежит в ' . $imagePath;
    }
}

$sql = "
    SELECT p.id, p.title, p.content, p.image_path, p.created_at, u.username
    FROM posts p
    INNER JOIN users u ON u.id = p.user_id
    WHERE p.user_id = $userId
    ORDER BY p.created_at DESC
";

$result = $mysqli->query($sql);
$posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Профиль</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <main class="page">
    <header class="navbar card">
      <div class="navbar-title"><?= $username ?></div>
      <nav class="nav-links">
        <a class="nav-link" href="index.php">Главная</a>
        <a class="nav-link active" href="profile.php">Профиль</a>
        <a class="nav-link" href="posts.php">Посты</a>
        <a class="nav-link" href="logout.php">Выход</a>
      </nav>
    </header>

    <section class="profile-layout">
      <div class="profile-top">
        <div class="profile-grid">
          <div class="card profile-mini">Здрваствуйте, меня зовут Бобышева Юлия, и я сделала этот сайт, а функционала добавления фото и описания профиля я не добавила! (И не буду)</div>
          <div class="card profile-mini">
            <div class="profile-photo">
              <img src="img/cat.jpg" alt="Фото профиля">
            </div>
          </div>
        </div>

        <button class="btn btn-outline" type="button" id="togglePhotoBtn">Открыть</button>

        <div class="hidden-photo card" id="extraPhoto">
          <img src="img/dog2.jpg" alt="Дополнительное фото" style="width:100%; display:block;">
        </div>
      </div>

      <div class="card" style="width:min(760px,100%); padding: 22px;">
        <?php if ($notice): ?><div class="success"><?= $notice ?></div><?php endif; ?>
        <?php if ($error): ?><div class="notice"><?= $error ?></div><?php endif; ?>

        <form class="form" action="" method="post" enctype="multipart/form-data">
          <h4 class="auth-heading">Новый пост</h4>

          <div class="field">
            <label class="label" for="post_title">Название поста</label>
            <input class="input" id="post_title" name="post_title" type="text" placeholder="Введите название поста" required>
          </div>

          <div class="field">
            <label class="label" for="post_body">Текст поста</label>
            <textarea class="textarea" id="post_body" name="post_body" placeholder="Введите текст поста" required></textarea>
          </div>

          <div class="field">
            <label class="label" for="post_file">Добавление файлов</label>
            <div class="upload-box">
              <input id="post_file" name="post_file" type="file" accept="image/*">
            </div>
          </div>

          <button class="btn" type="submit" name="save_post" value="1">Опубликовать</button>
        </form>
      </div>

      <div class="card post-card">
        <h2 class="post-title">Мои посты</h2>

        <?php if (!$posts): ?>
          <div class="notice">Постов пока нет.</div>
        <?php else: ?>
          <div class="post-list">
            <?php foreach ($posts as $post): ?>
              <a href="posts.php?id=<?= (int)$post['id'] ?>">
              <article class="post-item">
                <h3 class="post-title" style="font-size:20px; margin-bottom:8px;"><?= $post['title'] ?></h3>
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
      </div>
    </section>

    <script src="assets/js/profile.js"></script>
  </main>
</body>
</html>
