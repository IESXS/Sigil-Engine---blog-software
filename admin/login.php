<?php
require_once __DIR__ . '/../includes/functions.php';

if (current_user()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_user'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Usuário ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login Administrativo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container" style="max-width: 420px; margin-top: 50px;">
    <div class="card">
        <h2>Login Administrativo</h2>
        <?php if ($error): ?>
            <div class="notice"><?= sanitize($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="username">Usuário</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Entrar">
        </form>
    </div>
</div>
</body>
</html>
