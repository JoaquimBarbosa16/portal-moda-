<?php
require_once 'includes/config.php';
session_start_safe();
if(current_user()){header('Location: dashboard.php');exit;}

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email=strtolower(trim($_POST['email']??''));
    $pass=$_POST['pass']??'';
    if(!$email||!$pass){ $error='Preencha e-mail e senha.'; }
    else {
        $u=DB::row('SELECT * FROM users WHERE email=? AND active=1',[$email]);
        if($u && password_verify($pass,$u['password'])){
            unset($u['password']);
            $_SESSION['user']=$u;
            header('Location: dashboard.php');exit;
        } else { $error='E-mail ou senha incorretos.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VOGUE BR — Entrar</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,400&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{--noir:#0c0c0c;--gold:#c9a96e;--gold-dk:#a8833f;--rose:#c0655c;--border:#e2dcd4;--cream:#faf8f5;--silk:#f5f1eb;--white:#fff;--muted:#999;--text:#333}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;background:var(--cream)}
.split{display:flex;min-height:100vh;width:100%}
.visual{flex:1;background:linear-gradient(155deg,#080808 0%,#1a1000 50%,#0a1520 100%);display:flex;flex-direction:column;justify-content:flex-end;padding:56px;position:relative;overflow:hidden}
.visual-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(201,169,110,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(201,169,110,.05) 1px,transparent 1px);background-size:52px 52px}
.visual-fade{position:absolute;bottom:0;left:0;right:0;height:60%;background:linear-gradient(to top,rgba(8,8,8,.92),transparent)}
.visual-content{position:relative;z-index:2}
.brand{font-family:'Cormorant Garamond',serif;font-size:64px;font-weight:300;color:#fff;letter-spacing:10px;line-height:1}
.brand em{color:var(--gold);font-style:italic}
.rule{width:44px;height:1px;background:var(--gold);margin:20px 0}
.tagline{font-size:11px;letter-spacing:4px;text-transform:uppercase;color:rgba(255,255,255,.38)}
.quote{font-family:'Cormorant Garamond',serif;font-style:italic;font-size:19px;color:rgba(255,255,255,.25);max-width:340px;line-height:1.7;margin-top:22px}
.feats{margin-top:44px;display:flex;flex-direction:column;gap:16px}
.feat{display:flex;align-items:flex-start;gap:14px}
.feat-ic{width:36px;height:36px;border-radius:50%;background:rgba(201,169,110,.12);border:1px solid rgba(201,169,110,.22);display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--gold);flex-shrink:0}
.feat-txt strong{display:block;color:rgba(255,255,255,.78);font-size:13px;font-weight:500}
.feat-txt span{color:rgba(255,255,255,.3);font-size:11.5px}
.form-side{width:500px;flex-shrink:0;background:var(--white);display:flex;align-items:center;justify-content:center;padding:60px 52px}
.fw{width:100%}
.fw h2{font-family:'Cormorant Garamond',serif;font-size:34px;font-weight:400;color:var(--noir);margin-bottom:6px}
.fw .sub{color:var(--muted);font-size:13.5px;margin-bottom:34px}
.demo{background:var(--silk);border:1px solid var(--border);border-left:3px solid var(--gold);border-radius:4px;padding:14px 16px;margin-bottom:28px}
.demo strong{font-size:9.5px;letter-spacing:2px;text-transform:uppercase;color:var(--gold-dk);display:block;margin-bottom:9px}
.demo-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px}
.demo-row:last-child{margin-bottom:0}
.demo-lbl{font-size:11px;color:var(--muted);font-weight:500}
.demo-val{font-size:11.5px;color:var(--text);background:var(--white);padding:3px 10px;border-radius:3px;border:1px solid var(--border);font-family:monospace;cursor:pointer}
.demo-val:hover{border-color:var(--gold);color:var(--gold-dk)}
.fg{margin-bottom:18px}
.lbl{display:block;font-size:10.5px;letter-spacing:1.5px;text-transform:uppercase;font-weight:500;color:var(--text);margin-bottom:7px}
.inp{width:100%;padding:12px 14px;border:1px solid var(--border);border-radius:4px;background:var(--silk);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--noir);outline:none;transition:all .2s}
.inp:focus{border-color:var(--gold);background:var(--white);box-shadow:0 0 0 3px rgba(201,169,110,.1)}
.err{background:rgba(192,101,92,.08);border:1px solid rgba(192,101,92,.2);color:var(--rose);border-radius:4px;padding:10px 14px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.btn-sub{width:100%;padding:14px;background:var(--noir);color:var(--white);border:none;border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all .2s;font-weight:500;margin-top:4px}
.btn-sub:hover{background:#222;transform:translateY(-1px)}
.sw{text-align:center;font-size:13px;color:var(--muted);margin-top:20px}
.sw a{color:var(--gold-dk);font-weight:500;text-decoration:none}
@media(max-width:860px){.visual{display:none}.form-side{width:100%;padding:40px 24px}}
</style>
</head>
<body>
<div class="split">
  <div class="visual">
    <div class="visual-grid"></div>
    <div class="visual-fade"></div>
    <div class="visual-content">
      <div class="brand">VOGUE<em>BR</em></div>
      <div class="rule"></div>
      <div class="tagline">Fashion · Cultura · Elegância</div>
      <div class="quote">"A moda não é apenas roupas. É a linguagem que usamos para dizer ao mundo quem somos."</div>
      <div class="feats">
        <div class="feat"><div class="feat-ic">◉</div><div class="feat-txt"><strong>Portal editorial completo</strong><span>Notícias, tendências e entrevistas exclusivas</span></div></div>
        <div class="feat"><div class="feat-ic">◈</div><div class="feat-txt"><strong>Painel administrativo</strong><span>Gerencie usuários e conteúdo com facilidade</span></div></div>
        <div class="feat"><div class="feat-ic">⬡</div><div class="feat-txt"><strong>Banco de dados MySQL</strong><span>Cadastros e dados persistidos em tempo real</span></div></div>
      </div>
    </div>
  </div>
  <div class="form-side">
    <div class="fw">
      <h2>Bem-vinda de volta</h2>
      <p class="sub">Acesse sua conta para continuar</p>
      <div class="demo">
        <strong>✦ Contas de demonstração</strong>
        <div class="demo-row"><span class="demo-lbl">Admin:</span><span class="demo-val" onclick="fill('admin@vogue.com','admin123')">admin@vogue.com / admin123</span></div>
        <div class="demo-row"><span class="demo-lbl">Usuária:</span><span class="demo-val" onclick="fill('ana@gmail.com','ana123')">ana@gmail.com / ana123</span></div>
      </div>
      <?php if($error):?><div class="err">✕ <?=h($error)?></div><?php endif;?>
      <form method="POST">
        <div class="fg"><label class="lbl">E-mail</label><input class="inp" type="email" name="email" placeholder="seu@email.com" value="<?=h($_POST['email']??'ana@gmail.com')?>" required autofocus></div>
        <div class="fg"><label class="lbl">Senha</label><input class="inp" type="password" name="pass" placeholder="••••••••" value="ana123" required></div>
        <button type="submit" class="btn-sub">Entrar no portal</button>
      </form>
      <div class="sw">Não tem conta? <a href="register.php">Criar conta gratuita</a></div>
    </div>
  </div>
</div>
<script>function fill(e,p){document.querySelector('[name=email]').value=e;document.querySelector('[name=pass]').value=p;}</script>
</body>
</html>
