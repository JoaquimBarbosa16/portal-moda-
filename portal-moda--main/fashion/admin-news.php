<?php
require_once 'includes/config.php';
require_admin();
$pageTitle = 'Notícias (Admin)';
// Toggle status
if(($_GET['action']??'')==='toggle'&&($tid=(int)($_GET['id']??0))){
    DB::exec("UPDATE news SET status=IF(status='published','draft','published'), published_at=IF(status='draft',NOW(),NULL) WHERE id=?",[$tid]);
    header('Location: admin-news.php'); exit;
}
// Delete
if(($_GET['action']??'')==='delete'&&($did=(int)($_GET['id']??0))){
    DB::exec('DELETE FROM news WHERE id=?',[$did]); flash('info','Notícia excluída.'); header('Location: admin-news.php'); exit;
}
// Filters
$search=trim($_GET['q']??'');$catF=$_GET['cat']??'';$statusF=$_GET['status']??'';
$where='1=1';$params=[];
if($search){$where.=' AND (n.title LIKE ? OR n.excerpt LIKE ?)';$params[]="%$search%";$params[]="%$search%";}
if($catF){$where.=' AND n.category=?';$params[]=$catF;}
if($statusF){$where.=' AND n.status=?';$params[]=$statusF;}
$news=DB::query("SELECT n.*,u.name AS author_name FROM news n JOIN users u ON u.id=n.author_id WHERE $where ORDER BY n.id DESC",$params);
$total=(int)DB::scalar("SELECT COUNT(*) FROM news n JOIN users u ON u.id=n.author_id WHERE $where",$params);
$pub=(int)DB::scalar("SELECT COUNT(*) FROM news WHERE status='published'");
include 'includes/header.php';
?>
<div class="page-hd">
  <div class="page-hd-left"><h1>Notícias — Admin</h1><p><?=$total?> notícia(s) · <?=$pub?> publicadas no total</p></div>
  <div class="page-hd-actions"><a href="news-create.php" class="btn btn-primary">+ Nova Notícia</a></div>
</div>
<div class="tbl-filters">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="search-wrap" style="flex:1;min-width:180px"><span class="search-icon">🔍</span><input class="search-input" type="text" name="q" value="<?=h($search)?>" placeholder="Buscar…"></div>
    <select class="filter-sel" name="cat" onchange="this.form.submit()"><option value="">Todas categorias</option><?php foreach(['Moda','Beleza','Lifestyle','Tendências','Entrevistas','Eventos'] as $c):?><option <?=$catF===$c?'selected':''?>><?=h($c)?></option><?php endforeach;?></select>
    <select class="filter-sel" name="status" onchange="this.form.submit()"><option value="">Todos os status</option><option value="published" <?=$statusF==='published'?'selected':''?>>Publicados</option><option value="draft" <?=$statusF==='draft'?'selected':''?>>Rascunhos</option></select>
    <button class="btn btn-outline" type="submit">Buscar</button>
    <?php if($search||$catF||$statusF):?><a href="admin-news.php" class="btn btn-outline">✕</a><?php endif;?>
  </form>
</div>
<p style="font-size:11px;color:var(--text3);margin-bottom:10px">💡 Clique no badge de status para alternar rapidamente entre Publicado ↔ Rascunho.</p>
<div class="tbl-wrap"><table>
  <thead><tr><th>Img</th><th>Título</th><th>Categoria</th><th>Status</th><th>Views</th><th>Data</th><th>Ações</th></tr></thead>
  <tbody>
  <?php if(empty($news)):?>
  <tr><td colspan="7"><div style="text-align:center;padding:40px;color:var(--text3)"><div style="font-size:36px;opacity:.2;margin-bottom:12px">◆</div><p>Nenhuma notícia encontrada.</p></div></td></tr>
  <?php else:?>
  <?php foreach($news as $n):?>
  <tr>
    <td><img class="td-thumb" src="<?=h($n['image_url']??'')?>" onerror="this.style.background='var(--surface2)';this.src=''"></td>
    <td style="max-width:260px">
      <a href="news-view.php?id=<?=$n['id']?>" style="font-weight:500;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text);text-decoration:none"><?=h($n['title'])?></a>
      <div style="font-size:11px;color:var(--text3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px"><?=h(mb_substr($n['excerpt'],0,70))?>…</div>
    </td>
    <td style="font-size:12px;color:var(--text3)"><?=h($n['category'])?></td>
    <td>
      <a href="?action=toggle&id=<?=$n['id']?>" title="Clique para alternar" style="text-decoration:none">
        <span class="badge <?=$n['status']==='published'?'badge-pub':'badge-draft'?>"><?=$n['status']==='published'?'✓ Pub':'◌ Draft'?></span>
      </a>
    </td>
    <td style="font-size:12px;color:var(--text3)"><?=number_format($n['views'])?></td>
    <td style="font-size:12px;color:var(--text3);white-space:nowrap"><?=fmt_date($n['created_at'])?></td>
    <td><div class="td-actions">
      <a href="news-view.php?id=<?=$n['id']?>" class="btn btn-outline btn-sm">Ver</a>
      <a href="news-create.php?edit=<?=$n['id']?>" class="btn btn-outline btn-sm">✎</a>
      <a href="?action=delete&id=<?=$n['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir esta notícia?')">🗑</a>
    </div></td>
  </tr>
  <?php endforeach;?>
  <?php endif;?>
  </tbody>
</table></div>
<?php include 'includes/footer.php'; ?>
