<?php
require_once 'includes/config.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$n  = DB::row('SELECT n.*,u.name AS author_name FROM news n JOIN users u ON u.id=n.author_id WHERE n.id=?', [$id]);
if (!$n) { flash('error','Notícia não encontrada.'); header('Location: dashboard.php'); exit; }
// incrementa views
DB::exec('UPDATE news SET views = views + 1 WHERE id = ?', [$id]);
$pageTitle = $n['title'];
include 'includes/header.php';
?>
<div style="max-width:780px;margin:0 auto">
  <div style="margin-bottom:24px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <a href="dashboard.php" class="btn btn-outline btn-sm">← Voltar</a>
    <a href="news-create.php?edit=<?=$n['id']?>" class="btn btn-outline btn-sm">✎ Editar</a>
    <?php if(is_admin()):?>
    <a href="news-delete.php?id=<?=$n['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir esta notícia?')">🗑 Excluir</a>
    <?php endif;?>
  </div>
  <?php if($n['image_url']):?>
  <div style="height:400px;border-radius:var(--r);overflow:hidden;margin-bottom:28px;box-shadow:var(--shadow-md)">
    <img src="<?=h($n['image_url'])?>" alt="<?=h($n['title'])?>" style="width:100%;height:100%;object-fit:cover" onerror="this.parentElement.style.display='none'">
  </div>
  <?php endif;?>
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <span class="badge badge-<?=$n['status']==='published'?'pub':'draft'?>"><?=$n['status']==='published'?'Publicado':'Rascunho'?></span>
    <span style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:var(--gold);font-weight:600"><?=h($n['category'])?></span>
    <span style="font-size:12px;color:var(--text3)"><?=fmt_date($n['created_at'])?></span>
    <span style="font-size:12px;color:var(--text3)">· <?=number_format($n['views'])?> leituras</span>
  </div>
  <h1 style="font-family:'Cormorant Garamond',serif;font-size:clamp(26px,4vw,40px);font-weight:400;line-height:1.15;margin-bottom:18px;color:var(--text)"><?=h($n['title'])?></h1>
  <p style="font-family:'Cormorant Garamond',serif;font-size:19px;font-style:italic;color:var(--text2);line-height:1.65;margin-bottom:28px;padding-bottom:28px;border-bottom:1px solid var(--border)"><?=h($n['excerpt'])?></p>
  <div style="font-size:15.5px;line-height:1.85;color:var(--text2)">
    <?php foreach(explode("\n\n",$n['content']) as $p): if(trim($p)):?>
    <p style="margin-bottom:18px"><?=h(trim($p))?></p>
    <?php endif;endforeach;?>
  </div>
  <div style="margin-top:36px;padding-top:24px;border-top:1px solid var(--border);display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:8px">
      <div class="avatar" style="width:36px;height:36px;font-size:14px"><?=strtoupper($n['author_name'][0])?></div>
      <div><div style="font-size:13px;font-weight:500;color:var(--text)"><?=h($n['author_name'])?></div><div style="font-size:11px;color:var(--text3)">Autor</div></div>
    </div>
    <div style="margin-left:auto;display:flex;gap:8px">
      <a href="dashboard.php" class="btn btn-outline btn-sm">← Mais notícias</a>
      <a href="news-create.php?edit=<?=$n['id']?>" class="btn btn-primary btn-sm">✎ Editar</a>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
