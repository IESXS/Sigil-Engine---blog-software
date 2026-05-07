<?php
require_once __DIR__ . '/includes/functions.php';

$categorySlug = $_GET['category'] ?? '';
$categoryFilter = '';
$params = [];

if ($categorySlug) {
    $categoryFilter = 'WHERE categories.slug = ?';
    $params[] = $categorySlug;
}

$stmt = db()->prepare('SELECT posts.*, categories.name AS category_name, categories.slug AS category_slug FROM posts LEFT JOIN categories ON posts.category_id = categories.id ' . $categoryFilter . ' ORDER BY posts.created_at DESC');
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title><?= sanitize($siteName) ?></title>
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
        <div class="nav-bar">
            <strong>Categorias:</strong>
            <a href="index.php">Todas</a>
            <?php foreach ($categories as $cat): ?>
                <a href="index.php?category=<?= sanitize($cat['slug']) ?>"><?= sanitize($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php if (!$posts): ?>
            <p>Nenhum post encontrado. Faça login no painel administrativo para criar conteúdo.</p>
        <?php endif; ?>
        <?php foreach ($posts as $post): ?>
            <?php $thumbnail = get_post_thumbnail($post['id']); ?>
            <article class="post-summary">
                <?php if ($thumbnail): ?>
                    <a class="post-thumb" href="post.php?slug=<?= sanitize($post['slug']) ?>">
                        <img src="<?= sanitize($thumbnail) ?>" alt="Thumbnail do post <?= sanitize($post['title']) ?>">
                    </a>
                <?php endif; ?>
                <h2><a href="post.php?slug=<?= sanitize($post['slug']) ?>"><?= sanitize($post['title']) ?></a></h2>
                <small>Publicado em <?= sanitize($post['created_at']) ?> | Categoria: <?= sanitize($post['category_name'] ?: 'Sem categoria') ?></small>
                <p><?= sanitize(truncate_text(strip_tags($post['content']), 240)) ?></p>
                <p class="small-text">Curtidas: <?= (int)$post['likes'] ?> | Não gostei: <?= (int)$post['dislikes'] ?></p>
                <p>
                    <a class="button" href="like.php?post_id=<?= (int)$post['id'] ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Curtir</a>
                    <a class="button dislike-button" href="dislike.php?post_id=<?= (int)$post['id'] ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Não gostei</a>
                    <a class="button" href="post.php?slug=<?= sanitize($post['slug']) ?>">Ler post</a>
                </p>
            </article>
        <?php endforeach; ?>
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
