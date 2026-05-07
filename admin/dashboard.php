<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$totalPosts = db()->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$totalCategories = db()->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalComments = db()->query('SELECT COUNT(*) FROM comments')->fetchColumn();
$totalBanners = db()->query('SELECT COUNT(*) FROM banners')->fetchColumn();
$siteName = get_setting('site_name', SITE_NAME);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - <?= sanitize($siteName) ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <h2>Painel Administrativo</h2>
        <div style="margin-left:auto;"><a href="logout.php">Sair</a></div>
    </div>
</header>
<div class="container row">
    <aside class="sidebar card admin-card">
        <a href="dashboard.php">Dashboard</a><br>
        <a href="posts.php">Posts</a><br>
        <a href="categories.php">Categorias</a><br>
        <a href="banners.php">Banners</a><br>
        <a href="users.php">Usuários</a><br>
        <a href="settings.php">Configurações</a><br>
        <a href="logout.php">Sair</a>
    </aside>
    <main class="main card">
        <h3>Visão geral</h3>
        <p>Total de posts: <strong><?= (int)$totalPosts ?></strong></p>
        <p>Total de categorias: <strong><?= (int)$totalCategories ?></strong></p>
        <p>Total de comentários: <strong><?= (int)$totalComments ?></strong></p>
        <p>Total de banners: <strong><?= (int)$totalBanners ?></strong></p>
        <div class="notice">
            Use este painel para gerenciar posts, categorias, banners e configurações do site.
        </div>
    </main>
</div>
</body>
</html>
