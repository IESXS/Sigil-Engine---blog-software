<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = db()->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: posts.php');
    exit;
}

$posts = db()->query('SELECT posts.*, categories.name AS category_name FROM posts LEFT JOIN categories ON posts.category_id = categories.id ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Posts - Painel Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js"></script>
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <h2>Gerenciar Posts</h2>
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
        <a class="button" href="post_edit.php">Novo post</a>
        <table class="table-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= (int)$post['id'] ?></td>
                        <td><?= sanitize($post['title']) ?></td>
                        <td><?= sanitize($post['category_name'] ?: 'Sem categoria') ?></td>
                        <td><?= sanitize($post['created_at']) ?></td>
                        <td>
                            <a href="post_edit.php?id=<?= (int)$post['id'] ?>">Editar</a> |
                            <a href="#" onclick="if(confirmDelete('Excluir este post?')) window.location='posts.php?delete=<?= (int)$post['id'] ?>'; return false;">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
