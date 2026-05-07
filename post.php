<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: index.php');
    exit;
}

$stmt = db()->prepare('SELECT posts.*, categories.name AS category_name, categories.slug AS category_slug FROM posts LEFT JOIN categories ON posts.category_id = categories.id WHERE posts.slug = ? LIMIT 1');
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    if ($comment !== '') {
        $name = $name ?: 'Anônimo';
        $stmt = db()->prepare('INSERT INTO comments (post_id, name, comment, approved) VALUES (?, ?, ?, 1)');
        $stmt->execute([$post['id'], $name, $comment]);
        header('Location: post.php?slug=' . urlencode($slug) . '#comments');
        exit;
    }
}

$stmt = db()->prepare('SELECT * FROM post_images WHERE post_id = ? ORDER BY created_at ASC');
$stmt->execute([$post['id']]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$featuredImage = $images[0]['filename'] ?? '';
$otherImages = $images ? array_slice($images, 1) : [];

$stmt = db()->prepare('SELECT * FROM comments WHERE post_id = ? AND approved = 1 ORDER BY created_at DESC');
$stmt->execute([$post['id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = get_categories();
$bannersTop = get_banners_by_position('top', 1);
$bannersLeft = get_banners_by_position('left', 2);
$bannersRight = get_banners_by_position('right', 2);
$siteName = get_setting('site_name', SITE_NAME);
$siteDesc = get_setting('site_description', SITE_DESCRIPTION);
$logo = get_setting('logo');
$favicon = get_setting('favicon');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= sanitize($post['title']) ?> | <?= sanitize($siteName) ?></title>
    <meta name="description" content="<?= sanitize($siteDesc) ?>">
    <?php if ($favicon): ?>
        <link rel="icon" href="<?= sanitize($favicon) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <div class="logo">
            <?php if ($logo): ?>
                <img src="<?= sanitize($logo) ?>" alt="Logo do site">
            <?php else: ?>
                <h1><?= sanitize($siteName) ?></h1>
            <?php endif; ?>
        </div>
        <div style="margin-left:auto;">
            <a href="admin/login.php">Painel Admin</a>
        </div>
    </div>
</header>
<?php if ($bannersTop): ?>
<div class="top-banner card">
    <?php foreach ($bannersTop as $banner): ?>
        <div class="banner-list">
            <?php if (!empty($banner['url']) && $banner['url'] !== '#'): ?>
                <a href="<?= sanitize($banner['url']) ?>" target="_blank">
                    <img src="<?= UPLOAD_URL . '/' . sanitize($banner['filename']) ?>" alt="Banner topo">
                </a>
            <?php else: ?>
                <img src="<?= UPLOAD_URL . '/' . sanitize($banner['filename']) ?>" alt="Banner topo">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<div class="container row">
    <?php if ($bannersLeft): ?>
        <aside class="sidebar sidebar-left">
            <div class="card banner-card">
                <strong>Anúncios - Esquerda</strong>
                <?php foreach ($bannersLeft as $banner): ?>
                    <div class="banner-list">
                        <?php if (!empty($banner['url']) && $banner['url'] !== '#'): ?>
                            <a href="<?= sanitize($banner['url']) ?>" target="_blank">
                                <img src="<?= UPLOAD_URL . '/' . sanitize($banner['filename']) ?>" alt="Banner esquerdo">
                            </a>
                        <?php else: ?>
                            <img src="<?= UPLOAD_URL . '/' . sanitize($banner['filename']) ?>" alt="Banner esquerdo">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
    <?php endif; ?>
    <main class="main card">
        <article>
            <h2><?= sanitize($post['title']) ?></h2>
            <small>Publicado em <?= sanitize($post['created_at']) ?> | Categoria: <?= sanitize($post['category_name'] ?: 'Sem categoria') ?></small>
            <?php if ($featuredImage): ?>
                <div class="post-featured">
                    <img src="<?= UPLOAD_URL . '/' . sanitize($featuredImage) ?>" alt="Imagem principal do post">
                </div>
            <?php endif; ?>
            <div><?= nl2br(sanitize($post['content'])) ?></div>
            <?php if ($otherImages): ?>
                <div class="post-images" style="margin-top:16px;">
                    <?php foreach ($otherImages as $image): ?>
                        <img src="<?= UPLOAD_URL . '/' . sanitize($image['filename']) ?>" alt="Imagem do post">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <p class="small-text">Curtidas: <?= (int)$post['likes'] ?> | Não gostei: <?= (int)$post['dislikes'] ?></p>
            <p>
                <a class="button" href="like.php?post_id=<?= (int)$post['id'] ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Curtir</a>
                <a class="button dislike-button" href="dislike.php?post_id=<?= (int)$post['id'] ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Não gostei</a>
            </p>
        </article>
        <section id="comments" style="margin-top:24px;">
            <h3>Comentários</h3>
            <?php if ($comments): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card">
                        <strong><?= sanitize($comment['name']) ?></strong> <span class="small-text"><?= sanitize($comment['created_at']) ?></span>
                        <p><?= nl2br(sanitize($comment['comment'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Seja o primeiro a comentar.</p>
            <?php endif; ?>
            <form method="post">
                <label for="name">Nome (opcional)</label>
                <input type="text" id="name" name="name" placeholder="Anônimo">
                <label for="comment">Comentário</label>
                <textarea id="comment" name="comment" required></textarea>
                <input type="submit" value="Enviar comentário">
            </form>
        </section>
    </main>
    <?php if ($bannersRight): ?>
        <aside class="sidebar sidebar-right">
            <div class="card banner-card">
                <strong>Anúncios - Direita</strong>
                <?php foreach ($bannersRight as $banner): ?>
                    <div class="banner-list">
                        <?php if (!empty($banner['url']) && $banner['url'] !== '#'): ?>
                            <a href="<?= sanitize($banner['url']) ?>" target="_blank">
                                <img src="<?= UPLOAD_URL . '/' . sanitize($banner['filename']) ?>" alt="Banner direito">
                            </a>
                        <?php else: ?>
                            <img src="<?= UPLOAD_URL . '/' . sanitize($banner['filename']) ?>" alt="Banner direito">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
