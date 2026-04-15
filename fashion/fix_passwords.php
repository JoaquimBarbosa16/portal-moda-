<?php
/**
 * VOGUE BR — Corrigir senhas dos usuários demo
 * 
 * EXECUTE UMA ÚNICA VEZ após importar o dump:
 *   php fix_passwords.php
 *   ou acesse: http://localhost:8080/fix_passwords.php
 * 
 * APAGUE este arquivo após executar!
 */

require_once 'includes/config.php';

// Segurança mínima: só roda via CLI ou com token
$isCLI = (php_sapi_name() === 'cli');
$token = $_GET['token'] ?? '';
if (!$isCLI && $token !== 'voguebr_setup_2025') {
    die('<p style="font-family:sans-serif;padding:40px;color:#c00">
        Acesso negado. Use: <code>?token=voguebr_setup_2025</code><br>
        Ou execute via CLI: <code>php fix_passwords.php</code>
    </p>');
}

$users = [
    ['email' => 'admin@vogue.com', 'pass' => 'admin123', 'role' => 'admin'],
    ['email' => 'ana@gmail.com',   'pass' => 'ana123',   'role' => 'user'],
    ['email' => 'bea@gmail.com',   'pass' => 'bea123',   'role' => 'user'],
    ['email' => 'carla@gmail.com', 'pass' => 'carla123', 'role' => 'user'],
];

$ok = 0; $fail = 0; $log = [];

foreach ($users as $u) {
    $hash = password_hash($u['pass'], PASSWORD_DEFAULT);
    
    // Verifica se usuário existe
    $exists = DB::scalar('SELECT COUNT(*) FROM users WHERE email = ?', [$u['email']]);
    
    if ($exists) {
        // Atualiza hash
        DB::exec('UPDATE users SET password = ? WHERE email = ?', [$hash, $u['email']]);
        $log[] = "✓ Senha atualizada: {$u['email']}";
        $ok++;
    } else {
        // Cria usuário se não existir
        DB::insert(
            'INSERT INTO users (name, email, password, role, active, created_at) VALUES (?, ?, ?, ?, 1, NOW())',
            [ucfirst(explode('@', $u['email'])[0]), $u['email'], $hash, $u['role']]
        );
        $log[] = "✓ Usuário criado: {$u['email']}";
        $ok++;
    }
}

// Verifica login do admin
$admin = DB::row('SELECT * FROM users WHERE email = ?', ['admin@vogue.com']);
$loginOk = $admin && password_verify('admin123', $admin['password']);

if ($isCLI) {
    echo "\n═══════════════════════════════════════\n";
    echo "  VOGUE BR — Correção de Senhas\n";
    echo "═══════════════════════════════════════\n";
    foreach ($log as $l) echo "  $l\n";
    echo "───────────────────────────────────────\n";
    echo "  Resultado: $ok ok / $fail erros\n";
    echo "  Login admin: " . ($loginOk ? "✓ OK!" : "✗ FALHOU") . "\n";
    echo "═══════════════════════════════════════\n\n";
    echo "  ⚠ Apague este arquivo agora:\n";
    echo "     rm fix_passwords.php\n\n";
} else {
    $color = $loginOk ? '#5a9a6a' : '#c0655c';
    echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'>
    <title>Fix Passwords</title>
    <link href='https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap' rel='stylesheet'>
    <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'DM Sans',sans-serif;background:#faf8f5;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
    .box{background:#fff;border:1px solid #e2dcd4;border-radius:10px;padding:40px;max-width:540px;width:100%;box-shadow:0 8px 30px rgba(0,0,0,.08)}
    .logo{font-family:Georgia,serif;font-size:24px;letter-spacing:5px;color:#0c0c0c;margin-bottom:24px}
    .logo em{color:#c9a96e;font-style:italic}
    h2{font-size:20px;color:#0c0c0c;margin-bottom:6px}
    .sub{color:#999;font-size:13px;margin-bottom:24px}
    .log-item{padding:10px 14px;border-radius:4px;margin-bottom:8px;font-size:13px;background:#f0f8f2;color:#3a6a48;border:1px solid rgba(90,154,106,.2)}
    .status{padding:14px 18px;border-radius:6px;margin-top:20px;font-weight:500;font-size:14px;border:1px solid;background:" . ($loginOk ? '#f0f8f2' : '#fdf0ef') . ";color:$color;border-color:" . ($loginOk ? 'rgba(90,154,106,.3)' : 'rgba(192,101,92,.3)') . "}
    .warn{margin-top:20px;padding:12px 16px;background:#fff8e8;border:1px solid rgba(201,169,110,.3);border-radius:6px;font-size:12px;color:#8a6c35}
    .accounts{margin-top:20px;background:#f5f1eb;border-radius:6px;padding:16px;font-size:13px}
    .accounts strong{display:block;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#c9a96e;margin-bottom:10px}
    .acc-row{display:flex;justify-content:space-between;margin-bottom:6px;color:#444}
    code{background:#fff;padding:2px 8px;border-radius:3px;border:1px solid #e2dcd4;font-family:monospace;font-size:12px}
    .btn{display:block;margin-top:20px;padding:12px;background:#0c0c0c;color:#fff;border-radius:4px;text-align:center;text-decoration:none;font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:500}
    .btn:hover{background:#222}</style></head><body>
    <div class='box'>
      <div class='logo'>VOGUE<em>BR</em></div>
      <h2>Correção de Senhas</h2>
      <p class='sub'>Senhas geradas com o PHP desta máquina</p>";
    foreach ($log as $l) echo "<div class='log-item'>$l</div>";
    echo "<div class='status'>" . ($loginOk ? "✓ Login do admin verificado com sucesso!" : "✗ Houve um problema. Verifique a conexão com o banco.") . "</div>
    <div class='accounts'>
      <strong>✦ Contas configuradas</strong>
      <div class='acc-row'><span>admin@vogue.com</span><code>admin123</code></div>
      <div class='acc-row'><span>ana@gmail.com</span><code>ana123</code></div>
      <div class='acc-row'><span>bea@gmail.com</span><code>bea123</code></div>
      <div class='acc-row'><span>carla@gmail.com</span><code>carla123</code></div>
    </div>
    <div class='warn'>⚠ <strong>Importante:</strong> Apague este arquivo após usar! Ele não deve ficar acessível em produção.</div>
    <a class='btn' href='index.php'>Ir para o login →</a>
    </div></body></html>";
}
