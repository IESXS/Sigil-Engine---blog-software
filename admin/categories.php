<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        $slug = slugify($name);
        $stmt = db()->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
        $stmt->execute([$name, $slug]);
        header('Location: categories.php');
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = db()->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: categories.php');
    exit;
}

$categories = get_categories();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Categorias - Painel Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js"></script>
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <h2>Categorias</h2>
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
        <form method="post">
            <label for="name">Nova categoria</label>
            <input type="text" id="name" name="name" required>
            <input type="submit" value="Adicionar categoria">
        </form>
        <table class="table-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Slug</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= (int)$cat['id'] ?></td>
                        <td><?= sanitize($cat['name']) ?></td>
                        <td><?= sanitize($cat['slug']) ?></td>
                        <td><a href="#" onclick="if(confirmDelete('Excluir esta categoria?')) window.location='categories.php?delete=<?= (int)$cat['id'] ?>'; return false;">Excluir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
