<?php
/**
 * Script CLI para alterar credenciais de administrador
 * Uso: php change_admin.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

if (php_sapi_name() !== 'cli') {
    die("Este script só pode ser executado via CLI.\n");
}

echo "=== Alterar Credenciais de Administrador ===\n\n";

echo "Selecione uma opção:\n";
echo "1. Alterar senha de usuário existente\n";
echo "2. Criar novo usuário\n";
echo "3. Listar usuários\n";
echo "4. Excluir usuário\n";
echo "5. Sair\n";
echo "\nDigite sua opção: ";

$option = trim(fgets(STDIN));

switch ($option) {
    case 1:
        alterarSenha();
        break;
    case 2:
        criarUsuario();
        break;
    case 3:
        listarUsuarios();
        break;
    case 4:
        excluirUsuario();
        break;
    case 5:
        echo "Até logo!\n";
        break;
    default:
        echo "Opção inválida.\n";
}

function alterarSenha() {
    echo "\n=== Alterar Senha ===\n";
    echo "Nome de usuário: ";
    $username = trim(fgets(STDIN));
    
    $stmt = db()->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    if (!$stmt->fetch()) {
        echo "Erro: Usuário não encontrado.\n";
        return;
    }
    
    echo "Nova senha: ";
    system('stty -echo');
    $password = trim(fgets(STDIN));
    system('stty echo');
    echo "\n";
    
    if (strlen($password) < 6) {
        echo "Erro: Senha deve ter pelo menos 6 caracteres.\n";
        return;
    }
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('UPDATE users SET password = ? WHERE username = ?');
    $stmt->execute([$hash, $username]);
    
    echo "✓ Senha alterada com sucesso!\n";
}

function criarUsuario() {
    echo "\n=== Criar Novo Usuário ===\n";
    echo "Nome de usuário: ";
    $username = trim(fgets(STDIN));
    
    if (empty($username)) {
        echo "Erro: Nome de usuário não pode estar vazio.\n";
        return;
    }
    
    $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        echo "Erro: Este usuário já existe.\n";
        return;
    }
    
    echo "Senha: ";
    system('stty -echo');
    $password = trim(fgets(STDIN));
    system('stty echo');
    echo "\n";
    
    if (strlen($password) < 6) {
        echo "Erro: Senha deve ter pelo menos 6 caracteres.\n";
        return;
    }
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    
    echo "✓ Usuário criado com sucesso!\n";
}

function listarUsuarios() {
    echo "\n=== Usuários do Sistema ===\n";
    $stmt = db()->query('SELECT id, username, created_at FROM users ORDER BY created_at ASC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$users) {
        echo "Nenhum usuário encontrado.\n";
        return;
    }
    
    echo "\nID | Usuário | Criado em\n";
    echo str_repeat("-", 50) . "\n";
    foreach ($users as $user) {
        printf("%d  | %-20s | %s\n", $user['id'], $user['username'], $user['created_at']);
    }
}

function excluirUsuario() {
    echo "\n=== Excluir Usuário ===\n";
    
    $stmt = db()->query('SELECT COUNT(*) FROM users');
    if ($stmt->fetchColumn() <= 1) {
        echo "Erro: Não é possível excluir o único usuário do sistema.\n";
        return;
    }
    
    echo "Nome de usuário: ";
    $username = trim(fgets(STDIN));
    
    $stmt = db()->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    if (!$user = $stmt->fetch()) {
        echo "Erro: Usuário não encontrado.\n";
        return;
    }
    
    echo "Tem certeza? Digite 'sim' para confirmar: ";
    $confirm = trim(fgets(STDIN));
    
    if ($confirm === 'sim') {
        db()->prepare('DELETE FROM users WHERE username = ?')->execute([$username]);
        echo "✓ Usuário excluído com sucesso!\n";
    } else {
        echo "Cancelado.\n";
    }
}
