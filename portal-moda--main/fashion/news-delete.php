<?php
require_once 'includes/config.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    DB::exec('DELETE FROM news WHERE id = ?', [$id]);
    flash('info', 'Notícia excluída.');
}
header('Location: dashboard.php'); exit;
