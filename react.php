<?php
require_once __DIR__ . '/includes/functions.php';

$postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
$redirect = $_GET['redirect'] ?? 'index.php';
$type = $_GET['type'] ?? 'like';

if ($postId <= 0) {
    header('Location: ' . $redirect);
    exit;
}

if ($type === 'dislike') {
    $cookieName = 'disliked_post_' . $postId;
    if (!isset($_COOKIE[$cookieName])) {
        $stmt = db()->prepare('UPDATE posts SET dislikes = dislikes + 1 WHERE id = ?');
        $stmt->execute([$postId]);
        setcookie($cookieName, '1', time() + 86400 * 30, '/');
    }
} else {
    $cookieName = 'liked_post_' . $postId;
    if (!isset($_COOKIE[$cookieName])) {
        $stmt = db()->prepare('UPDATE posts SET likes = likes + 1 WHERE id = ?');
        $stmt->execute([$postId]);
        setcookie($cookieName, '1', time() + 86400 * 30, '/');
    }
}

header('Location: ' . $redirect);
exit;
