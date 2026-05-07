<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$message = '';
$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = db()->prepare('SELECT COUNT(*) FROM users');
    $stmt->execute();
    if ($stmt->fetchColumn() > 1) {
        db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
        header('Location: users.php');
        exit;
    } else {
        $error = 'Não é possível excluir o único usuário do sistema.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Todos os campos são obrigatórios.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Novas senhas não coincidem.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Senha deve ter pelo menos 6 caracteres.';
        } else {
            $stmt = db()->prepare('SELECT password FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([current_user()]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($currentPassword, $user['password'])) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = db()->prepare('UPDATE users SET password = ? WHERE username = ?');
                $stmt->execute([$hash, current_user()]);
                $message = 'Senha alterada com sucesso.';
            } else {
                $error = 'Senha atual incorreta.';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'add_user') {
        $newUsername = trim($_POST['new_username'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');

        if (empty($newUsername) || empty($newPassword)) {
            $error = 'Nome de usuário e senha são obrigatórios.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Senha deve ter pelo menos 6 caracteres.';
        } else {
            $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
            $stmt->execute([$newUsername]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Usuário já existe.';
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = db()->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                $stmt->execute([$newUsername, $hash]);
                $message = 'Novo usuário criado com sucesso.';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'change_username') {
        $newUsername = trim($_POST['change_username_value'] ?? '');

        if (empty($newUsername)) {
            $error = 'Nome de usuário é obrigatório.';
        } else {
            $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND username != ?');
            $stmt->execute([$newUsername, current_user()]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Este nome de usuário já existe.';
            } else {
                $stmt = db()->prepare('UPDATE users SET username = ? WHERE username = ?');
                $stmt->execute([$newUsername, current_user()]);
                $_SESSION['admin_user'] = $newUsername;
                $message = 'Nome de usuário alterado com sucesso.';
            }
        }
    }
}

$users = db()->query('SELECT id, username, created_at FROM users ORDER BY created_at ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - Painel Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js"></script>
</head>
<body>
<header>
    <div class="container row" style="align-items:center;">
        <h2>Gerenciar Usuários</h2>
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
            <div class="notice" style="background:#ccffcc;border-color:#99ff99;"><?= sanitize($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="notice" style="background:#ffcccc;border-color:#ff9999;"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <h3>Alterar Minha Senha</h3>
        <form method="post">
            <input type="hidden" name="action" value="change_password">
            <label for="current_password">Senha Atual</label>
            <input type="password" id="current_password" name="current_password" required>
            <label for="new_password">Nova Senha</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="confirm_password">Confirmar Senha</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <input type="submit" value="Alterar Senha">
        </form>

        <hr style="margin:24px 0;border:none;border-top:1px solid #bac3d0;">

        <h3>Alterar Meu Nome de Usuário</h3>
        <form method="post">
            <input type="hidden" name="action" value="change_username">
            <label for="change_username_value">Novo Nome de Usuário</label>
            <input type="text" id="change_username_value" name="change_username_value" value="<?= sanitize(current_user()) ?>" required>
            <input type="submit" value="Alterar Nome de Usuário">
        </form>

        <hr style="margin:24px 0;border:none;border-top:1px solid #bac3d0;">

        <h3>Adicionar Novo Usuário</h3>
        <form method="post">
            <input type="hidden" name="action" value="add_user">
            <label for="new_username">Nome de Usuário</label>
            <input type="text" id="new_username" name="new_username" required>
            <label for="new_password">Senha</label>
            <input type="password" id="new_password" name="new_password" required>
            <input type="submit" value="Criar Usuário">
        </form>

        <hr style="margin:24px 0;border:none;border-top:1px solid #bac3d0;">

        <h3>Usuários do Sistema</h3>
        <table class="table-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome de Usuário</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= (int)$user['id'] ?></td>
                        <td><?= sanitize($user['username']) ?><?= $user['username'] === current_user() ? ' <strong>(você)</strong>' : '' ?></td>
                        <td><?= sanitize($user['created_at']) ?></td>
                        <td>
                            <?php if ($user['username'] !== current_user() && count($users) > 1): ?>
                                <a href="#" onclick="if(confirmDelete('Excluir usuário <?= sanitize($user['username']) ?>?')) window.location='users.php?delete=<?= (int)$user['id'] ?>'; return false;">Excluir</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
