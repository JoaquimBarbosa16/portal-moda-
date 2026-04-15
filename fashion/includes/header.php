<?php
$user = current_user();
$page = basename($_SERVER['PHP_SELF'], '.php');
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VOGUE BR — <?= h($pageTitle ?? 'Portal') ?></title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#faf8f5;--surface:#fff;--surface2:#f5f1eb;--border:#e2dcd4;--border2:#ede8e0;
  --text:#0c0c0c;--text2:#444;--text3:#888;
  --gold:#c9a96e;--gold-dk:#a8833f;--gold-bg:rgba(201,169,110,.10);--gold-bg2:rgba(201,169,110,.06);
  --rose:#c0655c;--rose-bg:rgba(192,101,92,.10);
  --green:#5a9a6a;--green-bg:rgba(90,154,106,.10);
  --blue:#5a7fa8;--blue-bg:rgba(90,127,168,.10);
  --shadow-sm:0 1px 4px rgba(0,0,0,.06);--shadow-md:0 4px 20px rgba(0,0,0,.08);--shadow-lg:0 16px 60px rgba(0,0,0,.12);
  --r:8px;--r-sm:4px;--sidebar:260px;--header:66px;--ease:cubic-bezier(.4,0,.2,1);
}
[data-theme="dark"]{
  --bg:#0e0e0e;--surface:#181818;--surface2:#222;--border:#2e2e2e;--border2:#252525;
  --text:#f0ece4;--text2:#aaa;--text3:#666;
  --gold-bg:rgba(201,169,110,.12);--gold-bg2:rgba(201,169,110,.07);
  --rose-bg:rgba(192,101,92,.12);--green-bg:rgba(90,154,106,.12);--blue-bg:rgba(90,127,168,.12);
  --shadow-sm:0 1px 4px rgba(0,0,0,.3);--shadow-md:0 4px 20px rgba(0,0,0,.4);--shadow-lg:0 16px 60px rgba(0,0,0,.6);
}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);line-height:1.6;-webkit-font-smoothing:antialiased;transition:background .3s,color .3s}
a{color:inherit;text-decoration:none}
img{max-width:100%;display:block}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}

/* LAYOUT */
.app{display:flex;min-height:100vh;padding-top:var(--header)}
.main-wrap{flex:1;margin-left:var(--sidebar);min-height:calc(100vh - var(--header));display:flex;flex-direction:column}
.content{flex:1;padding:32px 40px;max-width:1280px;width:100%}

/* HEADER */
.header{position:fixed;top:0;left:0;right:0;z-index:200;height:var(--header);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;box-shadow:var(--shadow-sm);transition:background .3s,border-color .3s}
.h-logo{width:var(--sidebar);flex-shrink:0;display:flex;align-items:center;justify-content:center;border-right:1px solid var(--border);height:100%;padding:0 20px;font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:300;letter-spacing:5px;color:var(--text)}
.h-logo em{color:var(--gold);font-style:italic}
.h-body{flex:1;display:flex;align-items:center;justify-content:space-between;padding:0 24px}
.h-crumb{font-size:12px;color:var(--text3);display:flex;align-items:center;gap:6px}
.h-crumb span{color:var(--text2);font-weight:500}
.h-right{display:flex;align-items:center;gap:12px}

/* Botão Modo Escuro */
.theme-btn{
  display:flex;align-items:center;gap:7px;
  padding:6px 14px;border:1px solid var(--border);border-radius:100px;
  background:var(--surface2);cursor:pointer;transition:all .2s;
  font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.3px;
}
.theme-btn:hover{border-color:var(--gold);color:var(--text)}
.theme-btn .icon{font-size:15px;line-height:1}
.theme-btn .label{font-family:'DM Sans',sans-serif}

.avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--rose));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:#fff;flex-shrink:0}
.avatar.lg{width:80px;height:80px;font-size:30px}
.avatar.xl{width:96px;height:96px;font-size:36px}
.h-user{display:flex;align-items:center;gap:8px;padding:5px 14px 5px 6px;border:1px solid var(--border);border-radius:100px;cursor:pointer;transition:all .2s;background:var(--surface);text-decoration:none}
.h-user:hover{border-color:var(--gold)}
.h-user .name{font-size:13px;font-weight:500;color:var(--text)}
.h-user .role{font-size:10px;color:var(--text3)}
.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:100px;font-size:10px;font-weight:500;letter-spacing:.4px;text-transform:uppercase}
.badge-admin{background:var(--text);color:var(--gold)}
.badge-user{background:var(--blue-bg);color:var(--blue)}
.badge-pub{background:var(--green-bg);color:var(--green)}
.badge-draft{background:var(--surface2);color:var(--text3);border:1px solid var(--border)}
.btn-logout{font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:var(--text3);background:none;border:none;padding:6px 10px;border-radius:var(--r-sm);cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif}
.btn-logout:hover{color:var(--rose);background:var(--rose-bg)}

/* SIDEBAR */
.sidebar{position:fixed;top:var(--header);left:0;bottom:0;width:var(--sidebar);z-index:100;background:var(--surface);border-right:1px solid var(--border);overflow-y:auto;display:flex;flex-direction:column;padding:16px 0 40px;transition:background .3s,border-color .3s}
.sb-label{font-size:9px;letter-spacing:2.5px;text-transform:uppercase;color:var(--text3);font-weight:500;padding:16px 24px 6px;display:block}
.sb-item{display:flex;align-items:center;gap:11px;padding:10px 16px 10px 24px;font-size:13.5px;color:var(--text3);transition:all .2s;cursor:pointer;border:none;background:none;width:100%;text-align:left;border-left:2px solid transparent;text-decoration:none;font-family:'DM Sans',sans-serif}
.sb-item:hover{color:var(--text);background:var(--bg)}
.sb-item.active{color:var(--text);font-weight:500;border-left-color:var(--gold);background:var(--gold-bg2)}
.sb-icon{width:18px;text-align:center;font-size:15px;flex-shrink:0;opacity:.7}
.sb-item.active .sb-icon{opacity:1}
.sb-count{margin-left:auto;background:var(--surface2);color:var(--text3);font-size:10px;font-weight:600;padding:1px 7px;border-radius:100px;border:1px solid var(--border)}
.sb-div{height:1px;background:var(--border);margin:10px 20px}

/* COMPONENTES */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow-sm);transition:background .3s,border-color .3s}
.card-hd{padding:16px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between}
.card-hd h3{font-size:15px;font-weight:500;color:var(--text);font-family:'DM Sans',sans-serif}
.card-body{padding:22px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:var(--r-sm);font-size:12px;font-weight:500;letter-spacing:.4px;transition:all .2s;border:1px solid transparent;font-family:'DM Sans',sans-serif;cursor:pointer;white-space:nowrap;text-decoration:none}
.btn-primary{background:var(--text);color:var(--bg);border-color:var(--text)}
.btn-primary:hover{opacity:.85}
.btn-gold{background:var(--gold);color:#fff;border-color:var(--gold)}
.btn-gold:hover{background:var(--gold-dk)}
.btn-outline{background:var(--surface);color:var(--text2);border-color:var(--border)}
.btn-outline:hover{border-color:var(--text);color:var(--text)}
.btn-danger{background:var(--surface);color:var(--rose);border-color:rgba(192,101,92,.25)}
.btn-danger:hover{background:var(--rose);color:#fff;border-color:var(--rose)}
.btn-sm{padding:6px 14px;font-size:11px}
.btn-xs{padding:4px 10px;font-size:10px}
.form-group{margin-bottom:20px}
.form-label{display:block;font-size:10.5px;letter-spacing:1.5px;text-transform:uppercase;font-weight:500;color:var(--text2);margin-bottom:7px}
.form-hint{font-size:11px;color:var(--text3);margin-top:5px}
.form-ctrl{width:100%;padding:11px 14px;border:1px solid var(--border);border-radius:var(--r-sm);background:var(--surface2);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);outline:none;transition:all .2s}
.form-ctrl:focus{border-color:var(--gold);background:var(--surface);box-shadow:0 0 0 3px var(--gold-bg)}
.form-ctrl::placeholder{color:var(--text3)}
textarea.form-ctrl{resize:vertical;min-height:100px;line-height:1.6}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.form-full{grid-column:1/-1}
.form-actions{display:flex;gap:10px;padding-top:18px;border-top:1px solid var(--border2);margin-top:20px}
.tbl-wrap{border:1px solid var(--border);border-radius:var(--r);overflow:hidden;background:var(--surface)}
.tbl-filters{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap}
.search-wrap{position:relative;flex:1;min-width:180px}
.search-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text3);font-size:14px;pointer-events:none}
.search-input{width:100%;padding:9px 13px 9px 34px;border:1px solid var(--border);border-radius:var(--r-sm);background:var(--surface2);font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);outline:none;transition:all .2s}
.search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-bg)}
.filter-sel{padding:9px 14px;border:1px solid var(--border);border-radius:var(--r-sm);background:var(--surface2);font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text2);outline:none;cursor:pointer}
table{width:100%;border-collapse:collapse}
thead{background:var(--surface2)}
thead th{padding:11px 16px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;font-weight:600;color:var(--text3);text-align:left;border-bottom:1px solid var(--border)}
tbody tr{border-bottom:1px solid var(--border2);transition:background .15s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--gold-bg2)}
tbody td{padding:13px 16px;font-size:13.5px;color:var(--text2);vertical-align:middle}
td strong{color:var(--text)}
.td-thumb{width:50px;height:38px;object-fit:cover;border-radius:4px;background:var(--surface2)}
.td-actions{display:flex;gap:6px}
.flash{padding:12px 16px;border-radius:var(--r-sm);font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:500}
.flash-success{background:var(--green-bg);color:var(--green);border:1px solid rgba(90,154,106,.25)}
.flash-error{background:var(--rose-bg);color:var(--rose);border:1px solid rgba(192,101,92,.25)}
.flash-info{background:var(--gold-bg);color:var(--gold-dk);border:1px solid rgba(201,169,110,.25)}
.page-hd{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px;flex-wrap:wrap}
.page-hd-left h1{font-family:'Cormorant Garamond',serif;font-size:clamp(22px,3vw,34px);font-weight:400;color:var(--text)}
.page-hd-left p{color:var(--text3);font-size:14px;margin-top:4px}
.page-hd-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:22px;position:relative;overflow:hidden;transition:all .2s}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--accent,var(--gold))}
.stat-icon{width:42px;height:42px;border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;font-size:19px;margin-bottom:14px;background:var(--icon-bg,var(--gold-bg))}
.stat-num{font-family:'Cormorant Garamond',serif;font-size:44px;font-weight:300;line-height:1;color:var(--text);margin-bottom:4px}
.stat-label{font-size:12px;color:var(--text3)}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:500;align-items:center;justify-content:center;backdrop-filter:blur(3px)}
.modal-overlay.open{display:flex;animation:fadeUp .2s var(--ease)}
.modal{background:var(--surface);border-radius:12px;width:560px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lg)}
.modal-hd{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-hd h3{font-family:'Cormorant Garamond',serif;font-size:22px;color:var(--text)}
.modal-close{width:30px;height:30px;border-radius:50%;border:none;background:none;font-size:18px;color:var(--text3);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s}
.modal-close:hover{background:var(--surface2);color:var(--text)}
.modal-body{padding:24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border2);display:flex;gap:10px;justify-content:flex-end;background:var(--surface2)}
.profile-hero{background:linear-gradient(135deg,#0a0a0a 0%,#1a1000 100%);border-radius:12px;padding:36px;display:flex;align-items:center;gap:28px;margin-bottom:24px}
.ph-name{font-family:'Cormorant Garamond',serif;font-size:30px;color:#fff}
.ph-email{color:rgba(255,255,255,.45);font-size:13px;margin-top:3px}
.ph-bio{color:rgba(255,255,255,.35);font-size:13px;font-style:italic;margin-top:8px}
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
@media(max-width:768px){:root{--sidebar:0px;--header:56px}.sidebar{display:none}.content{padding:20px 16px}.stats-grid{grid-template-columns:1fr 1fr}.form-grid{grid-template-columns:1fr}}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="app">
<!-- HEADER -->
<header class="header">
  <a href="dashboard.php" class="h-logo" style="text-decoration:none">VOGUE<em>BR</em></a>
  <div class="h-body">
    <div class="h-crumb">
      <a href="dashboard.php" style="color:var(--text3);text-decoration:none">Portal</a>
      <span style="color:var(--border)">›</span>
      <span><?= h($pageTitle ?? 'Notícias') ?></span>
    </div>
    <div class="h-right">
      <!-- BOTÃO MODO ESCURO VISÍVEL -->
      <button class="theme-btn" id="themeBtn" onclick="toggleTheme()" title="Alternar modo escuro">
        <span class="icon" id="themeIcon">☀</span>
        <span class="label" id="themeLabel">Claro</span>
      </button>

      <?php if($user): ?>
      <a href="profile.php" class="h-user">
        <div class="avatar"><?= strtoupper($user['name'][0]) ?></div>
        <div>
          <div class="name"><?= h(explode(' ',$user['name'])[0]) ?></div>
          <div class="role"><?= $user['role']==='admin'?'Admin':'Leitora' ?></div>
        </div>
        <?php if($user['role']==='admin'):?><span class="badge badge-admin">Admin</span><?php endif;?>
      </a>
      <form method="POST" action="logout.php" style="display:inline">
        <button type="submit" class="btn-logout">Sair</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- SIDEBAR -->
<?php if($user): ?>
<?php
$totalNews  = (int)(DB::scalar("SELECT COUNT(*) FROM news WHERE status='published'") ?? 0);
$totalUsers = (int)(DB::scalar("SELECT COUNT(*) FROM users WHERE active=1") ?? 0);
?>
<aside class="sidebar">
  <div>
    <span class="sb-label">Navegar</span>
    <a class="sb-item <?=$page==='dashboard'?'active':''?>" href="dashboard.php"><span class="sb-icon">◉</span> Notícias <span class="sb-count"><?=$totalNews?></span></a>
    <a class="sb-item <?=$page==='news-create'?'active':''?>" href="news-create.php"><span class="sb-icon">✦</span> Nova Notícia</a>
    <a class="sb-item <?=$page==='profile'?'active':''?>" href="profile.php"><span class="sb-icon">◎</span> Meu Perfil</a>
  </div>
  <div class="sb-div"></div>
  <div>
    <span class="sb-label">Categorias</span>
    <?php foreach(['Moda','Beleza','Lifestyle','Tendências','Entrevistas','Eventos'] as $c):?>
    <a class="sb-item" href="dashboard.php?cat=<?=urlencode($c)?>"><span class="sb-icon" style="font-size:10px">—</span> <?=h($c)?></a>
    <?php endforeach;?>
  </div>
  <?php if(is_admin()):?>
  <div class="sb-div"></div>
  <div>
    <span class="sb-label">Administração</span>
    <a class="sb-item <?=$page==='admin'?'active':''?>" href="admin.php"><span class="sb-icon">◈</span> Painel Admin</a>
    <a class="sb-item <?=$page==='admin-users'?'active':''?>" href="admin-users.php"><span class="sb-icon">◷</span> Usuários <span class="sb-count"><?=$totalUsers?></span></a>
    <a class="sb-item <?=$page==='admin-news'?'active':''?>" href="admin-news.php"><span class="sb-icon">◆</span> Notícias (Admin)</a>
  </div>
  <?php endif;?>
</aside>
<?php endif;?>

<!-- MAIN -->
<div class="main-wrap">
<div class="content">
<?php if($flash):?>
<div class="flash flash-<?=h($flash['type'])?>">
  <?=$flash['type']==='success'?'✓':($flash['type']==='error'?'✕':'ℹ')?> <?=h($flash['msg'])?>
</div>
<?php endif;?>

<script>
// Tema
(function(){
  const saved = localStorage.getItem('vogue_theme') || 'light';
  document.documentElement.setAttribute('data-theme', saved);
  document.addEventListener('DOMContentLoaded', () => syncThemeBtn(saved));
})();
function syncThemeBtn(theme){
  const icon = document.getElementById('themeIcon');
  const label = document.getElementById('themeLabel');
  if(!icon||!label) return;
  if(theme==='dark'){icon.textContent='🌙';label.textContent='Escuro';}
  else{icon.textContent='☀';label.textContent='Claro';}
}
function toggleTheme(){
  const curr = document.documentElement.getAttribute('data-theme')||'light';
  const next = curr==='dark'?'light':'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('vogue_theme', next);
  syncThemeBtn(next);
}
</script>
