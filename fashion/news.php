<?php
// Redireciona para dashboard que agora exibe as notícias
require_once 'includes/config.php';
header('Location: dashboard.php?'.http_build_query($_GET));
exit;
