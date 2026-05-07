<?php
/**
 * Script de Configuração do SigilEngine
 * Execute uma única vez para configurar o banco de dados
 * 
 * Uso:
 *   - Via CLI: php setup.php
 *   - Via navegador: http://seusite.com/setup.php
 */

$isCliMode = php_sapi_name() === 'cli';
$forceSetup = false;
if ($isCliMode) {
    $forceSetup = in_array('--force', $_SERVER['argv'] ?? [], true);
} else {
    $forceSetup = isset($_GET['force']) && $_GET['force'] === '1';
}

// Verificar se já foi executado
if (file_exists(__DIR__ . '/.setup-done') && !$forceSetup) {
    if ($isCliMode) {
        die("Setup já foi executado anteriormente. Use --force para executar novamente.\n");
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Setup - CastleChan Blog</title>
        <style>
            body { font-family: Arial, sans-serif; background: #e4ebf5; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .notice { background: #fff7cc; border: 1px solid #f2d58b; padding: 20px; margin-top: 20px; }
        </style>
    </head>
    <body>
    <div class="container">
        <div class="notice">
            Setup já foi executado anteriormente.<br>
            Para executar novamente, adicione <strong>?force=1</strong> à URL ou delete o arquivo <code>.setup-done</code>.
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

if ($isCliMode) {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║      SigilEngine - Setup de Configuração                    ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
} else {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Setup - CastleChan Blog</title>
        <style>
            body { font-family: Arial, sans-serif; background: #e4ebf5; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #0070c0; border-bottom: 2px solid #0070c0; padding-bottom: 10px; }
            label { display: block; font-weight: bold; margin-top: 15px; }
            input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #9aa4b3; box-sizing: border-box; }
            button { background: #0070c0; color: white; padding: 12px 20px; border: none; border-radius: 3px; cursor: pointer; font-size: 16px; margin-top: 20px; }
            button:hover { background: #0057a0; }
            .success { background: #ccffcc; border: 1px solid #99ff99; padding: 15px; margin-top: 20px; color: #006600; }
            .error { background: #ffcccc; border: 1px solid #ff9999; padding: 15px; margin-top: 20px; color: #660000; }
            .info { background: #fff7cc; border: 1px solid #f2d58b; padding: 15px; margin-top: 20px; }
            hr { margin: 30px 0; border: none; border-top: 1px solid #ddd; }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Setup - CastleChan Blog</h1>
    <?php
}


if ($isCliMode) {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║      CastleChan Blog - Setup de Configuração              ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
} else {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Setup - CastleChan Blog</title>
        <style>
            body { font-family: Arial, sans-serif; background: #e4ebf5; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #0070c0; border-bottom: 2px solid #0070c0; padding-bottom: 10px; }
            label { display: block; font-weight: bold; margin-top: 15px; }
            input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #9aa4b3; box-sizing: border-box; }
            button { background: #0070c0; color: white; padding: 12px 20px; border: none; border-radius: 3px; cursor: pointer; font-size: 16px; margin-top: 20px; }
            button:hover { background: #0057a0; }
            .success { background: #ccffcc; border: 1px solid #99ff99; padding: 15px; margin-top: 20px; color: #006600; }
            .error { background: #ffcccc; border: 1px solid #ff9999; padding: 15px; margin-top: 20px; color: #660000; }
            .info { background: #fff7cc; border: 1px solid #f2d58b; padding: 15px; margin-top: 20px; }
            hr { margin: 30px 0; border: none; border-top: 1px solid #ddd; }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Setup - SigilEngine</h1>
    <?php
}

// Verificar se POST foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? '');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';
    $adminUser = trim($_POST['admin_user'] ?? '');
    $adminPass = $_POST['admin_pass'] ?? '';
    $siteName = trim($_POST['site_name'] ?? 'SigilEngine');
    $siteDesc = trim($_POST['site_description'] ?? 'Um blog simples estilo web1.0');

    $errors = [];

    if (empty($dbHost)) $errors[] = 'Host do banco de dados é obrigatório';
    if (empty($dbName)) $errors[] = 'Nome do banco de dados é obrigatório';
    if (empty($dbUser)) $errors[] = 'Usuário do banco de dados é obrigatório';
    if (empty($adminUser)) $errors[] = 'Usuário administrador é obrigatório';
    if (empty($adminPass) || strlen($adminPass) < 6) $errors[] = 'Senha deve ter pelo menos 6 caracteres';

    if (!empty($errors)) {
        if ($isCliMode) {
            echo "❌ Erros encontrados:\n";
            foreach ($errors as $err) {
                echo "   - $err\n";
            }
        } else {
            echo '<div class="error"><strong>Erros encontrados:</strong><ul>';
            foreach ($errors as $err) {
                echo '<li>' . htmlspecialchars($err) . '</li>';
            }
            echo '</ul></div>';
        }
    } else {
        // Conectar ao banco
        try {
            $pdo = new PDO(
                "mysql:host=$dbHost;charset=utf8mb4",
                $dbUser,
                $dbPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Criar banco de dados
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$dbName`");

            // Ler e executar SQL
            $sqlFile = __DIR__ . '/database.sql';
            if (!file_exists($sqlFile)) {
                throw new Exception('Arquivo database.sql não encontrado');
            }

            $sql = file_get_contents($sqlFile);
            $sql = preg_replace('/--.*?(\r?\n|$)/', "\n", $sql);
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if ($statement === '') {
                    continue;
                }
                $pdo->exec($statement);
            }

            // Verificar se usuário admin existe
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
            $stmt->execute([$adminUser]);
            if ($stmt->fetchColumn() == 0) {
                $hash = password_hash($adminPass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                $stmt->execute([$adminUser, $hash]);
            }

            // Inserir/atualizar configurações
            $pdo->exec("INSERT IGNORE INTO settings (name, value) VALUES
                ('site_name', '" . addslashes($siteName) . "'),
                ('site_description', '" . addslashes($siteDesc) . "'),
                ('logo', ''),
                ('favicon', '')");

            // Atualizar config.php
            $configContent = "<?php\n";
            $configContent .= "// Configuração do SigilEngine - Gerado pelo setup.php\n\n";
            $configContent .= "define('DB_HOST', '" . addslashes($dbHost) . "');\n";
            $configContent .= "define('DB_NAME', '" . addslashes($dbName) . "');\n";
            $configContent .= "define('DB_USER', '" . addslashes($dbUser) . "');\n";
            $configContent .= "define('DB_PASS', '" . addslashes($dbPass) . "');\n\n";
            $configContent .= "define('SITE_NAME', '" . addslashes($siteName) . "');\n";
            $configContent .= "define('SITE_DESCRIPTION', '" . addslashes($siteDesc) . "');\n\n";
            $configContent .= "define('ADMIN_USER', '" . addslashes($adminUser) . "');\n";
            $configContent .= "define('ADMIN_PASS', '" . addslashes($adminPass) . "');\n\n";
            $configContent .= "define('UPLOAD_DIR', __DIR__ . '/uploads');\n";
            $configContent .= "define('UPLOAD_URL', 'uploads');\n\n";
            $configContent .= "define('MAX_UPLOAD_SIZE', 4 * 1024 * 1024); // 4 MB por arquivo\n\n";
            $configContent .= "if (!is_dir(UPLOAD_DIR)) {\n";
            $configContent .= "    mkdir(UPLOAD_DIR, 0755, true);\n";
            $configContent .= "}\n";

            file_put_contents(__DIR__ . '/config.php', $configContent);

            // Marcar como completo
            file_put_contents(__DIR__ . '/.setup-done', date('Y-m-d H:i:s'));

            // Sucesso!
            if ($isCliMode) {
                echo "✓ Setup concluído com sucesso!\n";
                echo "  Banco de dados: $dbName\n";
                echo "  Usuário admin: $adminUser\n";
                echo "  Nome do site: $siteName\n";
                echo "\n  → Acesse: http://seusite.com/admin/login.php\n";
                echo "  → Use as credenciais criadas para fazer login\n\n";
            } else {
                echo '<div class="success">';
                echo '<strong>✓ Setup concluído com sucesso!</strong><br><br>';
                echo 'Banco de dados criado e configurado.<br>';
                echo '<strong>Dados de acesso:</strong><br>';
                echo 'Usuário: ' . htmlspecialchars($adminUser) . '<br>';
                echo 'Senha: [Conforme configurado]<br><br>';
                echo '<a href="index.php" style="color: #0070c0;">→ Ir para o site</a> | ';
                echo '<a href="admin/login.php" style="color: #0070c0;">→ Ir para o painel admin</a>';
                echo '</div>';
            }
        } catch (PDOException $e) {
            $msg = 'Erro ao conectar ao banco: ' . $e->getMessage();
            if ($isCliMode) {
                echo "❌ $msg\n";
            } else {
                echo '<div class="error"><strong>Erro:</strong> ' . htmlspecialchars($msg) . '</div>';
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            if ($isCliMode) {
                echo "❌ $msg\n";
            } else {
                echo '<div class="error"><strong>Erro:</strong> ' . htmlspecialchars($msg) . '</div>';
            }
        }
    }

    if (!$isCliMode) {
        echo '</div></body></html>';
    }
    exit;
}

// Formulário (apenas via web)
if (!$isCliMode) {
    ?>
        <form method="post">
            <h2>Configuração do Banco de Dados</h2>
            
            <label for="db_host">Host do MySQL</label>
            <input type="text" id="db_host" name="db_host" value="localhost" required>
            
            <label for="db_name">Nome do Banco de Dados</label>
            <input type="text" id="db_name" name="db_name" value="castlechan" required>
            
            <label for="db_user">Usuário MySQL</label>
            <input type="text" id="db_user" name="db_user" value="root" required>
            
            <label for="db_pass">Senha MySQL</label>
            <input type="password" id="db_pass" name="db_pass">
            
            <hr>
            <h2>Dados do Administrador</h2>
            
            <label for="admin_user">Usuário Admin</label>
            <input type="text" id="admin_user" name="admin_user" value="admin" required>
            
            <label for="admin_pass">Senha Admin (mínimo 6 caracteres)</label>
            <input type="password" id="admin_pass" name="admin_pass" required>
            
            <hr>
            <h2>Informações do Site</h2>
            
            <label for="site_name">Nome do Site</label>
            <input type="text" id="site_name" name="site_name" value="CastleChan Blog">
            
            <label for="site_description">Descrição do Site</label>
            <textarea id="site_description" name="site_description">Um blog simples estilo web1.0</textarea>
            
            <button type="submit">Executar Setup</button>
        </form>
        
        <div class="info">
            <strong>ℹ Informações:</strong><br>
            Este script criará todas as tabelas necessárias e configurará o arquivo config.php.
            Execute apenas uma vez.
        </div>
    </div>
    </body>
    </html>
    <?php
} else {
    // Modo CLI - mostrar formulário interativo
    echo "Este script pode ser executado de duas formas:\n\n";
    echo "1. Via navegador:\n";
    echo "   http://seusite.com/setup.php\n\n";
    echo "2. Via CLI (interativo):\n";
    echo "   php setup.php --interactive\n\n";
    echo "Argumentos CLI disponíveis:\n";
    echo "   --host=localhost\n";
    echo "   --name=castlechan\n";
    echo "   --user=root\n";
    echo "   --pass=senha\n";
    echo "   --admin=admin\n";
    echo "   --admin-pass=senha123\n";
    echo "   --site-name=\"Meu Site\"\n";
    echo "   --site-desc=\"Descrição\"\n\n";
}
