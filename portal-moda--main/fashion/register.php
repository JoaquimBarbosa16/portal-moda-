<?php
require_once 'includes/config.php';
session_start_safe();
if(current_user()){header('Location: dashboard.php');exit;}

$error='';$success='';$vals=['name'=>'','email'=>''];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name  = trim($_POST['name']??'');
    $email = strtolower(trim($_POST['email']??''));
    $pass  = $_POST['pass']??'';
    $pass2 = $_POST['pass2']??'';
    $vals  = compact('name','email');

    if(!$name||!$email||!$pass)         { $error='Preencha todos os campos.'; }
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) { $error='E-mail inválido.'; }
    elseif(strlen($pass)<6)             { $error='Senha deve ter pelo menos 6 caracteres.'; }
    elseif($pass !== $pass2)            { $error='As senhas não coincidem.'; }
    else {
        // Verifica duplicado
        $exists = DB::scalar('SELECT COUNT(*) FROM users WHERE email=?', [$email]);
        if($exists){
            $error='Este e-mail já está cadastrado. <a href="index.php" style="color:var(--gold-dk)">Fazer login</a>';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $id = DB::insert(
                'INSERT INTO users (name,email,password,role,created_at) VALUES (?,?,?,?,NOW())',
                [$name, $email, $hash, 'user']
            );
            // Loga automaticamente
            $newUser = DB::row('SELECT * FROM users WHERE id=?', [$id]);
            unset($newUser['password']);
            $_SESSION['user'] = $newUser;
            flash('success', "Conta criada com sucesso! Bem-vinda, {$name}!");
            header('Location: dashboard.php'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VOGUE BR — Criar Conta</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{--noir:#0c0c0c;--gold:#c9a96e;--gold-dk:#a8833f;--rose:#c0655c;--border:#e2dcd4;--cream:#faf8f5;--silk:#f5f1eb;--white:#fff;--muted:#999;--text:#333;--green:#5a9a6a}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;min-height:100vh;background:var(--cream);display:flex;align-items:center;justify-content:center;padding:40px 16px}
.card{background:var(--white);border:1px solid var(--border);border-radius:10px;padding:44px;width:100%;max-width:500px;box-shadow:0 8px 40px rgba(0,0,0,.07)}
.logo{font-family:'Cormorant Garamond',serif;font-size:26px;letter-spacing:6px;color:var(--noir);text-align:center;margin-bottom:28px}
.logo em{color:var(--gold);font-style:italic}
h2{font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:400;color:var(--noir);margin-bottom:5px}
.sub{color:var(--muted);font-size:13.5px;margin-bottom:28px}
.fg{margin-bottom:16px}
.lbl{display:block;font-size:10.5px;letter-spacing:1.5px;text-transform:uppercase;font-weight:500;color:var(--text);margin-bottom:7px}
.inp{width:100%;padding:12px 14px;border:1px solid var(--border);border-radius:4px;background:var(--silk);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--noir);outline:none;transition:all .2s}
.inp:focus{border-color:var(--gold);background:var(--white);box-shadow:0 0 0 3px rgba(201,169,110,.1)}
.hint{font-size:11px;color:var(--muted);margin-top:5px}
.err{background:rgba(192,101,92,.08);border:1px solid rgba(192,101,92,.2);color:var(--rose);border-radius:4px;padding:10px 14px;font-size:13px;margin-bottom:16px}
.req{display:flex;flex-direction:column;gap:6px;background:#f0f8f2;border:1px solid rgba(90,154,106,.2);border-radius:4px;padding:12px 14px;margin-bottom:18px;font-size:12px;color:#3a6a48}
.req li{list-style:none;display:flex;align-items:center;gap:6px}
.req li::before{content:'○';font-size:10px;color:var(--muted)}
.req li.ok::before{content:'✓';color:var(--green)}
.btn{width:100%;padding:13px;background:var(--noir);color:var(--white);border:none;border-radius:4px;font-family:'DM Sans',sans-serif;font-size:12px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all .2s;font-weight:500;margin-top:4px}
.btn:hover{background:#222;transform:translateY(-1px)}
.sw{text-align:center;font-size:13px;color:var(--muted);margin-top:20px}
.sw a{color:var(--gold-dk);font-weight:500;text-decoration:none}
</style>
</head>
<body>
<div class="card">
  <div class="logo">VOGUE<em>BR</em></div>
  <h2>Criar conta</h2>
  <p class="sub">Preencha os dados abaixo — é rápido e gratuito</p>

  <?php if($error):?><div class="err">✕ <?=$error?></div><?php endif;?>

  <ul class="req" id="reqList">
    <li id="req-name">Nome com pelo menos 2 caracteres</li>
    <li id="req-email">E-mail válido</li>
    <li id="req-pass">Senha com 6+ caracteres</li>
    <li id="req-pass2">Confirmação de senha igual</li>
  </ul>

  <form method="POST" id="regForm">
    <div class="fg">
      <label class="lbl">Nome completo</label>
      <input class="inp" type="text" name="name" id="fName" placeholder="Seu nome completo" value="<?=h($vals['name'])?>" required oninput="check()">
    </div>
    <div class="fg">
      <label class="lbl">E-mail</label>
      <input class="inp" type="email" name="email" id="fEmail" placeholder="seu@email.com" value="<?=h($vals['email'])?>" required oninput="check()">
    </div>
    <div class="fg">
      <label class="lbl">Senha</label>
      <input class="inp" type="password" name="pass" id="fPass" placeholder="Mínimo 6 caracteres" required oninput="check()">
      <div class="hint" id="passStrength"></div>
    </div>
    <div class="fg">
      <label class="lbl">Confirmar senha</label>
      <input class="inp" type="password" name="pass2" id="fPass2" placeholder="Repita a senha" required oninput="check()">
    </div>
    <button type="submit" class="btn" id="submitBtn">Criar minha conta</button>
  </form>
  <div class="sw">Já tem conta? <a href="index.php">Entrar</a></div>
</div>
<script>
function check(){
  const name=document.getElementById('fName').value.trim();
  const email=document.getElementById('fEmail').value.trim();
  const pass=document.getElementById('fPass').value;
  const pass2=document.getElementById('fPass2').value;
  const set=(id,ok)=>{const el=document.getElementById(id);el.classList.toggle('ok',ok)};
  set('req-name',name.length>=2);
  set('req-email',/^[^@]+@[^@]+\.[^@]+$/.test(email));
  set('req-pass',pass.length>=6);
  set('req-pass2',pass.length>=6&&pass===pass2);
  // strength
  const ps=document.getElementById('passStrength');
  if(!pass){ps.textContent='';return;}
  let s=0;if(pass.length>=8)s++;if(/[A-Z]/.test(pass))s++;if(/[0-9]/.test(pass))s++;if(/[^a-zA-Z0-9]/.test(pass))s++;
  const labels=['','Fraca','Média','Boa','Forte'];
  const colors=['','#c00','#e07','#c9a96e','#5a9a6a'];
  ps.innerHTML=`Força: <span style="color:${colors[s]||colors[1]};font-weight:500">${labels[s]||labels[1]}</span>`;
}
check();
</script>
</body>
</html>
