<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['banner'])) {
    $file = upload_file($_FILES['banner'], 'banner_');
    $url = trim($_POST['url'] ?? '');
    $position = in_array($_POST['position'] ?? 'left', ['left', 'right', 'top'], true) ? $_POST['position'] : 'left';
    $active = isset($_POST['active']) ? 1 : 0;
    if ($file) {
        $stmt = db()->prepare('INSERT INTO banners (filename, url, active, position) VALUES (?, ?, ?, ?)');
        $stmt->execute([$file, $url, $active, $position]);
    }
    header('Location: banners.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = db()->prepare('SELECT filename FROM banners WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($banner) {
        @unlink(UPLOAD_DIR . '/' . $banner['filename']);
        db()->prepare('DELETE FROM banners WHERE id = ?')->execute([$id]);
    }
    header('Location: banners.php');
    exit;
}

$banners = db()->query('SELECT * FROM banners ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Banners - Painel Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js"></script>
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <h2>Banners</h2>
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
        <form method="post" enctype="multipart/form-data">
            <label for="banner">Arquivo do banner</label>
            <input type="file" id="banner" name="banner" accept="image/*" required>
            <small>Recomendado: laterais 240x600px, topo 768x300px.</small>
            <label for="url">URL do anúncio</label>
            <input type="text" id="url" name="url" placeholder="https://exemplo.com">
            <label for="position">Posição do banner</label>
            <select id="position" name="position">
                <option value="left">Lado esquerdo</option>
                <option value="right">Lado direito</option>
                <option value="top">Topo (skyscraper)</option>
            </select>
            <label><input type="checkbox" name="active" checked> Ativo</label>
            <input type="submit" value="Enviar banner">
        </form>
        <h3>Banners cadastrados</h3>
        <table class="table-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>URL</th>
                    <th>Posição</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td><?= (int)$banner['id'] ?></td>
                        <td><img src="<?= UPLOAD_URL . '/' . sanitize($banner['filename']) ?>" style="max-height:60px;"></td>
                        <td><a href="<?= sanitize($banner['url']) ?>" target="_blank"><?= sanitize($banner['url']) ?></a></td>
                        <td><?= sanitize($banner['position']) ?></td>
                        <td><?= $banner['active'] ? 'Sim' : 'Não' ?></td>
                        <td><a href="#" onclick="if(confirmDelete('Excluir este banner?')) window.location='banners.php?delete=<?= (int)$banner['id'] ?>'; return false;">Excluir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
