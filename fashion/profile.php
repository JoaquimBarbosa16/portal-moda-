<?php
require_once 'includes/config.php';
require_login();
$pageTitle = 'Meu Perfil';
$user = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['pass']  ?? '';
    $bio   = trim($_POST['bio']   ?? '');
    if (!$name || !$email) $errors[] = 'Nome e e-mail são obrigatórios.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
    elseif ($pass && strlen($pass) < 6) $errors[] = 'Senha deve ter 6+ caracteres.';
    elseif ((int)DB::scalar('SELECT COUNT(*) FROM users WHERE email=? AND id!=?', [$email, $user['id']])) $errors[] = 'E-mail já em uso.';
    if (!$errors) {
        if ($pass) {
            DB::exec('UPDATE users SET name=?,email=?,bio=?,password=?,updated_at=NOW() WHERE id=?',
                [$name,$email,$bio,password_hash($pass,PASSWORD_DEFAULT),$user['id']]);
        } else {
            DB::exec('UPDATE users SET name=?,email=?,bio=?,updated_at=NOW() WHERE id=?', [$name,$email,$bio,$user['id']]);
        }
        $fresh = DB::row('SELECT * FROM users WHERE id=?', [$user['id']]);
        unset($fresh['password']);
        $_SESSION['user'] = $fresh;
        $user = $fresh;
        flash('success', 'Perfil atualizado com sucesso!');
        header('Location: profile.php'); exit;
    }
}
$myNews = DB::query('SELECT * FROM news WHERE author_id=?', [$user['id']]);
$myPub  = array_filter($myNews, fn($n) => $n['status'] === 'published');
include 'includes/header.php';
?>
<div class="page-hd"><div class="page-hd-left"><h1>Meu Perfil</h1><p>Gerencie suas informações pessoais</p></div></div>

<div class="profile-hero">
  <div class="avatar xl"><?=strtoupper($user['name'][0])?></div>
  <div>
    <div class="ph-name"><?=h($user['name'])?></div>
    <div class="ph-email"><?=h($user['email'])?></div>
    <?php if(!empty($user['bio'])):?><div class="ph-bio">"<?=h($user['bio'])?>"</div><?php endif;?>
    <div style="display:flex;gap:8px;margin-top:14px;align-items:center">
      <span class="badge <?=$user['role']==='admin'?'badge-admin':'badge-user'?>"><?=$user['role']==='admin'?'Admin':'Leitora'?></span>
      <span style="font-size:11px;color:rgba(255,255,255,.3)">Membro desde <?=(new DateTime($user['created_at']))->format('M/Y')?></span>
    </div>
  </div>
</div>

<?php if($errors):?><div class="flash flash-error">✕ <?=implode(' | ',array_map('h',$errors))?></div><?php endif;?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
  <div class="card"><div class="card-hd"><h3>Editar Perfil</h3></div><div class="card-body">
    <form method="POST">
      <div class="form-group"><label class="form-label">Nome completo</label><input class="form-ctrl" type="text" name="name" value="<?=h($user['name'])?>" required></div>
      <div class="form-group"><label class="form-label">E-mail</label><input class="form-ctrl" type="email" name="email" value="<?=h($user['email'])?>" required></div>
      <div class="form-group"><label class="form-label">Bio</label><textarea class="form-ctrl" name="bio" rows="3" placeholder="Conte um pouco sobre você…"><?=h($user['bio']??'')?></textarea></div>
      <div class="form-group"><label class="form-label">Nova Senha</label><input class="form-ctrl" type="password" name="pass" placeholder="Deixe vazio para manter"><div class="form-hint">Mínimo 6 caracteres.</div></div>
      <div class="form-actions"><button type="submit" class="btn btn-primary">💾 Salvar Alterações</button></div>
    </form>
  </div></div>

  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="card"><div class="card-hd"><h3>Minhas Estatísticas</h3></div><div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div style="text-align:center;padding:18px;background:var(--surface2);border-radius:var(--r-sm);border:1px solid var(--border)">
        <div style="font-family:'Cormorant Garamond',serif;font-size:38px;color:var(--text)"><?=count($myNews)?></div>
        <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.8px;margin-top:4px">Notícias</div>
      </div>
      <div style="text-align:center;padding:18px;background:var(--surface2);border-radius:var(--r-sm);border:1px solid var(--border)">
        <div style="font-family:'Cormorant Garamond',serif;font-size:38px;color:var(--gold)"><?=count($myPub)?></div>
        <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.8px;margin-top:4px">Publicadas</div>
      </div>
    </div></div>

    <div class="card"><div class="card-hd"><h3>Ações Rápidas</h3></div><div class="card-body" style="display:flex;flex-direction:column;gap:8px">
      <a href="news-create.php" class="btn btn-primary" style="justify-content:center">+ Criar nova notícia</a>
      <a href="dashboard.php"   class="btn btn-outline" style="justify-content:center">Ver todas as notícias</a>
      <?php if(is_admin()):?><a href="admin.php" class="btn btn-gold" style="justify-content:center">⬡ Painel Admin</a><?php endif;?>
      <a href="logout.php" class="btn btn-danger" style="justify-content:center">Sair da conta</a>
    </div></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
