<?php
session_start();
require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro de conexão com o banco de dados: ' . $e->getMessage());
}

function db() {
    global $pdo;
    return $pdo;
}

function get_setting($name, $default = '') {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE name = ? LIMIT 1');
    $stmt->execute([$name]);
    $result = $stmt->fetchColumn();
    return $result !== false ? $result : $default;
}

function set_setting($name, $value) {
    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
    return $stmt->execute([$name, $value]);
}

function sanitize($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function slugify($text) {
    $text = preg_replace('~[^\pL\pN]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text ?: 'item-'.time();
}

function current_user() {
    return $_SESSION['admin_user'] ?? false;
}

function require_admin() {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function upload_file(array $file, $prefix = '') {
    if (empty($file['name']) || empty($file['tmp_name'])) {
        return '';
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return '';
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'];
    if (!in_array($ext, $allowed, true)) {
        return '';
    }

    $name = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $filename = $prefix . time() . '_' . $name . '.' . $ext;
    $target = UPLOAD_DIR . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }

    return '';
}

function upload_files(array $files, $prefix = '') {
    $uploaded = [];
    foreach ($files['name'] as $index => $name) {
        if (empty($name)) {
            continue;
        }
        $file = [
            'name' => $files['name'][$index],
            'tmp_name' => $files['tmp_name'][$index],
            'size' => $files['size'][$index],
            'error' => $files['error'][$index],
            'type' => $files['type'][$index],
        ];

        $saved = upload_file($file, $prefix);
        if ($saved) {
            $uploaded[] = $saved;
        }
    }
    return $uploaded;
}

function get_categories() {
    $pdo = db();
    $stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_random_banners($limit = 2) {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM banners WHERE active = 1 AND filename != "" ORDER BY RAND() LIMIT ?');
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_banners_by_position($position, $limit = 2) {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM banners WHERE active = 1 AND position = ? AND filename != "" ORDER BY RAND() LIMIT ?');
    $stmt->bindValue(1, $position, PDO::PARAM_STR);
    $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function has_table($table) {
    $pdo = db();
    $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
    $stmt->execute([$table]);
    return (bool)$stmt->fetch();
}

function has_table_column($table, $column) {
    $pdo = db();
    $stmt = $pdo->prepare('SHOW COLUMNS FROM `' . $table . '` LIKE ?');
    $stmt->execute([$column]);
    return (bool)$stmt->fetch();
}

function ensure_banner_position_schema() {
    if (!has_table('banners')) {
        return;
    }
    if (!has_table_column('banners', 'position')) {
        db()->exec('ALTER TABLE banners ADD COLUMN position VARCHAR(20) NOT NULL DEFAULT "left"');
    }
}

function truncate_text($text, $length = 220) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

ensure_banner_position_schema();

function ensure_database_schema() {
    if (!has_table('posts')) {
        return;
    }
    $pdo = db();
    if (!has_table_column('posts', 'dislikes')) {
        $pdo->exec('ALTER TABLE posts ADD COLUMN dislikes INT DEFAULT 0');
    }
}

function get_post_thumbnail($postId) {
    ensure_database_schema();
    $pdo = db();
    $stmt = $pdo->prepare('SELECT filename FROM post_images WHERE post_id = ? ORDER BY created_at ASC LIMIT 1');
    $stmt->execute([$postId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    return $image ? UPLOAD_URL . '/' . $image['filename'] : '';
}

ensure_database_schema();
