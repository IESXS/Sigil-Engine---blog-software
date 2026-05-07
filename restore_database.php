<?php
/**
 * Script para Restaurar Banco de Dados
 * Execute para restaurar a estrutura original do banco
 * 
 * Uso:
 *   php restore_database.php
 */

if (php_sapi_name() !== 'cli') {
    die("Este script só pode ser executado via CLI.\n");
}

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║      CastleChan Blog - Restaurar Banco de Dados           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "⚠️  Aviso: Esta operação irá:\n";
    echo "   1. Excluir TODOS os posts, comentários e imagens\n";
    echo "   2. Limpar banners\n";
    echo "   3. Manter usuários e configurações intactos\n\n";
    
    echo "Digite 'restaurar' para confirmar ou outro valor para cancelar: ";
    $confirm = trim(fgets(STDIN));

    if ($confirm !== 'restaurar') {
        echo "Operação cancelada.\n\n";
        exit;
    }

    echo "\nProcessando...\n";

    // Desabilitar chaves estrangeiras temporariamente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Limpar tabelas (mantendo estrutura)
    $pdo->exec("TRUNCATE TABLE post_images");
    $pdo->exec("TRUNCATE TABLE banners");
    $pdo->exec("TRUNCATE TABLE comments");
    $pdo->exec("TRUNCATE TABLE posts");
    $pdo->exec("TRUNCATE TABLE categories");

    // Reabilitar chaves estrangeiras
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "✓ Banco de dados restaurado com sucesso!\n";
    echo "\nDados removidos:\n";
    echo "   ✓ Todos os posts\n";
    echo "   ✓ Todos os comentários\n";
    echo "   ✓ Todas as imagens de posts (registros)\n";
    echo "   ✓ Todos os banners\n";
    echo "   ✓ Todas as categorias\n";
    echo "\nDados preservados:\n";
    echo "   ✓ Usuários\n";
    echo "   ✓ Configurações do site\n";
    echo "   ✓ Arquivos em uploads/ (não foram deletados)\n";
    echo "\n";

} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n\n";
    exit(1);
}
