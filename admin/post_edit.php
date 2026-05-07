<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        header('Location: posts.php');
        exit;
    }
}

$categories = get_categories();
$error = '';

if (isset($_GET['delete_image'])) {
    $imageId = (int)$_GET['delete_image'];
    $stmt = db()->prepare('SELECT filename FROM post_images WHERE id = ? AND post_id = ? LIMIT 1');
    $stmt->execute([$imageId, $id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($image) {
        @unlink(UPLOAD_DIR . '/' . $image['filename']);
        db()->prepare('DELETE FROM post_images WHERE id = ?')->execute([$imageId]);
    }
    header('Location: post_edit.php?id=' . $id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    $content = trim($_POST['content'] ?? '');
    $categoryId = $_POST['category_id'] ? (int)$_POST['category_id'] : null;

    if ($title === '' || $content === '') {
        $error = 'Título e conteúdo são obrigatórios.';
    } else {
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE posts SET title = ?, slug = ?, content = ?, category_id = ? WHERE id = ?');
            $stmt->execute([$title, $slug, $content, $categoryId, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO posts (title, slug, content, category_id) VALUES (?, ?, ?, ?)');
            $stmt->execute([$title, $slug, $content, $categoryId]);
            $id = db()->lastInsertId();
        }

        if (!empty($_FILES['images'])) {
            $uploaded = upload_files($_FILES['images'], 'post_');
            foreach ($uploaded as $filename) {
                $stmt = db()->prepare('INSERT INTO post_images (post_id, filename) VALUES (?, ?)');
                $stmt->execute([$id, $filename]);
            }
        }

        header('Location: posts.php');
        exit;
    }
}

$images = [];
if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM post_images WHERE post_id = ? ORDER BY created_at ASC');
    $stmt->execute([$id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $post ? 'Editar Post' : 'Novo Post' ?> - Painel Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js"></script>
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <h2><?= $post ? 'Editar Post' : 'Novo Post' ?></h2>
        <div style="margin-left:auto;"><a href="posts.php">Voltar</a> | <a href="logout.php">Sair</a></div>
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
        <?php if ($error): ?>
            <div class="notice"><?= sanitize($error) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label for="title">Título</label>
            <input type="text" id="title" name="title" value="<?= sanitize($post['title'] ?? '') ?>" required>
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?= sanitize($post['slug'] ?? '') ?>" placeholder="deixe vazio para gerar automaticamente">
            <label for="category_id">Categoria</label>
            <select id="category_id" name="category_id">
                <option value="">Sem categoria</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= isset($post['category_id']) && $post['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="content">Conteúdo</label>
            <textarea id="content" name="content" required><?= sanitize($post['content'] ?? '') ?></textarea>
            <label for="images">Imagens do post (múltiplas)</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple>
            <?php if ($images): ?>
                <h4>Imagens atuais</h4>
                <?php foreach ($images as $image): ?>
                    <div class="banner-list">
                        <img src="<?= UPLOAD_URL . '/' . sanitize($image['filename']) ?>" alt="Imagem do post">
                        <p><a href="post_edit.php?id=<?= (int)$id ?>&delete_image=<?= (int)$image['id'] ?>" onclick="return confirmDelete('Remover esta imagem?');">Remover</a></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <input type="submit" value="Salvar post">
        </form>
    </main>
</div>
</body>
</html>
