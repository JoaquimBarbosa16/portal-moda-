<?php
require_once 'includes/config.php';
require_admin();
$pageTitle = 'Painel Admin';
$totalUsers=(int)DB::scalar("SELECT COUNT(*) FROM users WHERE active=1");
$totalNews=(int)DB::scalar("SELECT COUNT(*) FROM news");
$pubNews=(int)DB::scalar("SELECT COUNT(*) FROM news WHERE status='published'");
$draftNews=(int)DB::scalar("SELECT COUNT(*) FROM news WHERE status='draft'");
$admins=(int)DB::scalar("SELECT COUNT(*) FROM users WHERE role='admin'");
$recentUsers=DB::query("SELECT id,name,email,role,created_at FROM users ORDER BY id DESC LIMIT 5");
$recentNews=DB::query("SELECT id,title,status,created_at FROM news ORDER BY id DESC LIMIT 5");
include 'includes/header.php';
?>
<div class="page-hd">
  <div class="page-hd-left"><h1>Painel <em style="color:var(--gold);font-style:italic">Administrativo</em></h1><p>Visão geral e controle completo do portal</p></div>
</div>
<div class="stats-grid">
  <div class="stat-card" style="--accent:var(--gold);--icon-bg:var(--gold-bg)"><div class="stat-icon">◷</div><div class="stat-num"><?=$totalUsers?></div><div class="stat-label">Usuários ativos</div><div style="font-size:11px;color:var(--text3);margin-top:6px">↑ <?=$admins?> admin(s)</div></div>
  <div class="stat-card" style="--accent:var(--rose);--icon-bg:var(--rose-bg)"><div class="stat-icon">◉</div><div class="stat-num"><?=$totalNews?></div><div class="stat-label">Total de notícias</div><div style="font-size:11px;color:var(--text3);margin-top:6px">↑ <?=$pubNews?> publicadas</div></div>
  <div class="stat-card" style="--accent:var(--green);--icon-bg:var(--green-bg)"><div class="stat-icon">◆</div><div class="stat-num"><?=$pubNews?></div><div class="stat-label">Publicadas</div><div style="font-size:11px;color:var(--green);margin-top:6px">Visíveis no portal</div></div>
  <div class="stat-card"><div class="stat-icon">◈</div><div class="stat-num"><?=$draftNews?></div><div class="stat-label">Rascunhos</div><div style="font-size:11px;color:var(--text3);margin-top:6px">Aguardando revisão</div></div>
</div>
<div style="background:linear-gradient(135deg,var(--text) 0%,#1a1000 100%);border-radius:var(--r);padding:24px 28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:28px">
  <div><div style="font-family:'Cormorant Garamond',serif;font-size:22px;color:#fff;margin-bottom:4px">Ações Rápidas</div><div style="font-size:13px;color:rgba(255,255,255,.4)">Gerencie o portal com eficiência</div></div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="admin-users.php" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.2)">👥 Usuários</a>
    <a href="admin-news.php" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.2)">📰 Notícias</a>
    <a href="news-create.php" class="btn btn-gold">+ Nova Notícia</a>
    <a href="admin-users.php?action=new" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.2)">+ Usuário</a>
  </div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
  <div class="card"><div class="card-hd"><h3>Usuários Recentes</h3><a href="admin-users.php" class="btn btn-outline btn-xs">Ver todos</a></div>
    <table><thead><tr><th>Nome</th><th>Perfil</th><th>Cadastro</th></tr></thead><tbody>
    <?php foreach($recentUsers as $u):?><tr>
      <td><div style="display:flex;align-items:center;gap:8px"><div class="avatar" style="width:28px;height:28px;font-size:11px"><?=strtoupper($u['name'][0])?></div><strong><?=h($u['name'])?></strong></div></td>
      <td><span class="badge <?=$u['role']==='admin'?'badge-admin':'badge-user'?>"><?=$u['role']==='admin'?'Admin':'Usuário'?></span></td>
      <td style="font-size:12px;color:var(--text3)"><?=fmt_date($u['created_at'])?></td>
    </tr><?php endforeach;?>
    </tbody></table>
  </div>
  <div class="card"><div class="card-hd"><h3>Notícias Recentes</h3><a href="admin-news.php" class="btn btn-outline btn-xs">Ver todas</a></div>
    <table><thead><tr><th>Título</th><th>Status</th><th>Data</th></tr></thead><tbody>
    <?php foreach($recentNews as $n):?><tr>
      <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><a href="news-view.php?id=<?=$n['id']?>" style="font-weight:500;color:var(--text)"><?=h($n['title'])?></a></td>
      <td><span class="badge <?=$n['status']==='published'?'badge-pub':'badge-draft'?>"><?=$n['status']==='published'?'Pub.':'Draft'?></span></td>
      <td style="font-size:12px;color:var(--text3)"><?=fmt_date($n['created_at'])?></td>
    </tr><?php endforeach;?>
    </tbody></table>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
