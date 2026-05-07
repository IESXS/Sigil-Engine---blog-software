<?php
// Configuração do CastleChan Blog
// Este arquivo é gerado automaticamente pelo setup.php
// Não edite este arquivo diretamente. Use setup.php para reconfigurar.

define('DB_HOST', 'localhost');
define('DB_NAME', 'castlechan');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'CastleChan Blog');
define('SITE_DESCRIPTION', 'Um blog simples estilo web1.0 com painel administrativo.');

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin');

define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', 'uploads');

define('MAX_UPLOAD_SIZE', 4 * 1024 * 1024); // 4 MB por arquivo

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
