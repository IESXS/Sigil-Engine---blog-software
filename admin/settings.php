<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = trim($_POST['site_name'] ?? '');
    $siteDesc = trim($_POST['site_description'] ?? '');
    if ($siteName !== '') {
        set_setting('site_name', $siteName);
        set_setting('site_description', $siteDesc);
        $message = 'Configurações salvas.';
    }
    if (!empty($_FILES['logo']['name'])) {
        $logoFile = upload_file($_FILES['logo'], 'logo_');
        if ($logoFile) {
            set_setting('logo', UPLOAD_URL . '/' . $logoFile);
        }
    }
    if (!empty($_FILES['favicon']['name'])) {
        $faviconFile = upload_file($_FILES['favicon'], 'favicon_');
        if ($faviconFile) {
            set_setting('favicon', UPLOAD_URL . '/' . $faviconFile);
        }
    }
    header('Location: settings.php');
    exit;
}

$currentSiteName = get_setting('site_name', SITE_NAME);
$currentSiteDesc = get_setting('site_description', SITE_DESCRIPTION);
$currentLogo = get_setting('logo');
$currentFavicon = get_setting('favicon');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Configurações - Painel Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <h2>Configurações</h2>
        <div style="margin-left:auto;"><a href="dashboard.php">Voltar</a> | <a href="logout.php">Sair</a></div>
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
    </aside>
    <main class="main card">
        <?php if ($message): ?>
            <div class="notice"><?= sanitize($message) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label for="site_name">Nome do site</label>
            <input type="text" id="site_name" name="site_name" value="<?= sanitize($currentSiteName) ?>" required>
            <label for="site_description">Descrição do site</label>
            <textarea id="site_description" name="site_description"><?= sanitize($currentSiteDesc) ?></textarea>
            <label for="logo">Logo</label>
            <input type="file" id="logo" name="logo" accept="image/*">
            <?php if ($currentLogo): ?>
                <p><img src="<?= sanitize($currentLogo) ?>" style="max-height:70px;"></p>
            <?php endif; ?>
            <label for="favicon">Favicon</label>
            <input type="file" id="favicon" name="favicon" accept="image/x-icon,image/png,image/svg+xml">
            <?php if ($currentFavicon): ?>
                <p><img src="<?= sanitize($currentFavicon) ?>" style="max-height:40px;"></p>
            <?php endif; ?>
            <input type="submit" value="Salvar configurações">
        </form>
    </main>
</div>
</body>
</html>
