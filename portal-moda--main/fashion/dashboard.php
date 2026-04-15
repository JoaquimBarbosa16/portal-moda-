<?php
require_once 'includes/config.php';
require_login();
$pageTitle = 'Notícias';
$user = current_user();

// ─── Filtros ─────────────────────────────────────────────────
$cat    = trim($_GET['cat']    ?? '');
$search = trim($_GET['q']      ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// ─── Categorias disponíveis ───────────────────────────────────
$cats = ['Moda','Beleza','Lifestyle','Tendências','Entrevistas','Eventos'];

// ─── Query dinâmica ───────────────────────────────────────────
$where = 'n.status = ?';
$params = ['published'];

if ($cat) {
    $where .= ' AND n.category = ?';
    $params[] = $cat;
}
if ($search) {
    $where .= ' AND (n.title LIKE ? OR n.excerpt LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$baseSql = "
    SELECT n.id, n.title, n.slug, n.category, n.excerpt, n.image_url,
           n.views, n.published_at, n.created_at,
           u.name AS author_name
    FROM news n
    JOIN users u ON u.id = n.author_id
    WHERE $where
    ORDER BY n.published_at DESC, n.created_at DESC
";

// paginação manual
$total   = (int) DB::scalar("SELECT COUNT(*) FROM news n JOIN users u ON u.id=n.author_id WHERE $where", $params);
$offset  = ($page - 1) * $perPage;
$news    = DB::query("$baseSql LIMIT $perPage OFFSET $offset", $params);
$lastPage = max(1, (int) ceil($total / $perPage));

// destaque: a mais recente
$featured = !empty($news) ? array_shift($news) : null;

// stats para o header
$totalNews  = (int) DB::scalar("SELECT COUNT(*) FROM news WHERE status='published'");
$totalUsers = (int) DB::scalar("SELECT COUNT(*) FROM users WHERE active=1");

include 'includes/header.php';
?>

<!-- ════════════════════════════════════════════════
     BARRA DE CATEGORIAS (TIPO PORTAL DE NOTÍCIAS)
═══════════════════════════════════════════════════ -->
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid var(--border)">
  <a href="dashboard.php"
     style="padding:6px 16px;border-radius:100px;font-size:12px;font-weight:500;letter-spacing:.4px;
            background:<?=$cat===''?'var(--text)':'var(--surface2)'?>;
            color:<?=$cat===''?'var(--bg)':'var(--text3)'?>;
            border:1px solid <?=$cat===''?'var(--text)':'var(--border)'?>;
            text-decoration:none;transition:all .2s">Todas</a>
  <?php foreach($cats as $c): ?>
  <a href="?cat=<?=urlencode($c)?>"
     style="padding:6px 16px;border-radius:100px;font-size:12px;font-weight:500;letter-spacing:.4px;
            background:<?=$cat===$c?'var(--text)':'var(--surface2)'?>;
            color:<?=$cat===$c?'var(--bg)':'var(--text3)'?>;
            border:1px solid <?=$cat===$c?'var(--text)':'var(--border)'?>;
            text-decoration:none;transition:all .2s"><?=h($c)?></a>
  <?php endforeach; ?>

  <!-- BUSCA inline -->
  <form method="GET" style="margin-left:auto;display:flex;gap:6px">
    <?php if($cat):?><input type="hidden" name="cat" value="<?=h($cat)?>"> <?php endif;?>
    <input type="text" name="q" value="<?=h($search)?>" placeholder="Buscar notícias…"
           style="padding:7px 14px;border:1px solid var(--border);border-radius:100px;background:var(--surface2);
                  font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);outline:none;width:200px">
    <button type="submit" class="btn btn-outline btn-sm" style="border-radius:100px">🔍</button>
    <?php if($search||$cat):?>
    <a href="dashboard.php" class="btn btn-outline btn-sm" style="border-radius:100px">✕</a>
    <?php endif;?>
  </form>
</div>

<!-- RESULTADO / CONTEXTO -->
<div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px">
  <h1 style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:400;color:var(--text)">
    <?php if($search): ?>Resultados para "<em style="color:var(--gold)"><?=h($search)?></em>"
    <?php elseif($cat): ?><em style="color:var(--gold)"><?=h($cat)?></em>
    <?php else: ?>Últimas Notícias<?php endif; ?>
  </h1>
  <span style="font-size:13px;color:var(--text3)"><?=$total?> notícia<?=$total!==1?'s':''?> encontrada<?=$total!==1?'s':''?></span>
</div>

<?php if(!$featured && !$news): ?>
  <div style="text-align:center;padding:80px 20px;color:var(--text3)">
    <div style="font-size:48px;opacity:.2;margin-bottom:16px">◉</div>
    <p style="font-size:15px">Nenhuma notícia encontrada<?=$search?" para \"$search\"":'.'?></p>
    <a href="dashboard.php" class="btn btn-outline" style="margin-top:16px;display:inline-flex">Limpar filtros</a>
  </div>
<?php else: ?>

<?php if($featured && $page===1 && !$search && !$cat): ?>
<!-- ════ DESTAQUE PRINCIPAL ════ -->
<div style="margin-bottom:32px;border-radius:var(--r);overflow:hidden;border:1px solid var(--border);background:var(--surface);box-shadow:var(--shadow-md);display:grid;grid-template-columns:1.5fr 1fr">
  <div style="position:relative;min-height:380px;overflow:hidden">
    <img src="<?=h($featured['image_url']??'')?>" alt="<?=h($featured['title'])?>"
         style="width:100%;height:100%;object-fit:cover;transition:transform .5s"
         onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform=''"
         onerror="this.parentElement.style.background='var(--surface2)';this.style.display='none'">
    <div style="position:absolute;bottom:18px;left:18px;background:var(--text);color:var(--gold);font-size:9px;letter-spacing:2px;text-transform:uppercase;padding:5px 12px;border-radius:2px"><?=h($featured['category'])?></div>
  </div>
  <div style="padding:36px 32px;display:flex;flex-direction:column;justify-content:center">
    <div style="font-size:9.5px;letter-spacing:2px;text-transform:uppercase;color:var(--gold);font-weight:600;margin-bottom:12px">✦ Destaque Editorial</div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:600;color:var(--text);line-height:1.25;margin-bottom:14px"><?=h($featured['title'])?></h2>
    <p style="font-size:14px;color:var(--text2);line-height:1.7;margin-bottom:22px"><?=h(mb_substr($featured['excerpt'],0,180))?>…</p>
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;font-size:12px;color:var(--text3)">
      <span>Por <?=h($featured['author_name'])?></span>
      <span>·</span>
      <span><?=fmt_relative($featured['published_at']??$featured['created_at'])?></span>
      <span>·</span>
      <span><?=number_format($featured['views'])?> leituras</span>
    </div>
    <a href="news-view.php?id=<?=$featured['id']?>" class="btn btn-primary" style="align-self:flex-start">Ler notícia completa →</a>
  </div>
</div>
<?php elseif($featured): array_unshift($news,$featured);$featured=null; ?>
<?php endif; ?>

<!-- ════ GRID DE NOTÍCIAS ════ -->
<?php if(!empty($news) || ($page===1&&$featured)): ?>
<?php if($page>1||$search||$cat) array_unshift($news,...($featured?[$featured]:[])); ?>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:32px">
  <?php foreach($news as $n): ?>
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;transition:all .25s;display:flex;flex-direction:column;box-shadow:var(--shadow-sm)"
       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='transparent'"
       onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">
    <!-- Imagem -->
    <div style="position:relative;height:200px;overflow:hidden;background:var(--surface2)">
      <img src="<?=h($n['image_url']??'')?>" alt="<?=h($n['title'])?>"
           style="width:100%;height:100%;object-fit:cover;transition:transform .5s;display:block"
           onerror="this.style.display='none'">
      <div style="position:absolute;bottom:10px;left:10px;background:var(--text);color:var(--gold);font-size:9px;letter-spacing:1.5px;text-transform:uppercase;padding:4px 10px;border-radius:2px"><?=h($n['category'])?></div>
    </div>
    <!-- Corpo -->
    <div style="padding:18px 20px 20px;flex:1;display:flex;flex-direction:column">
      <a href="news-view.php?id=<?=$n['id']?>" style="font-family:'Cormorant Garamond',serif;font-size:17px;font-weight:600;color:var(--text);line-height:1.35;margin-bottom:8px;display:block;text-decoration:none"><?=h($n['title'])?></a>
      <p style="font-size:13px;color:var(--text3);line-height:1.65;flex:1;margin-bottom:14px"><?=h(mb_substr($n['excerpt'],0,100))?>…</p>
      <div style="display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--text3);padding-top:12px;border-top:1px solid var(--border2)">
        <div style="display:flex;align-items:center;gap:6px">
          <div style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--rose));display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;color:#fff"><?=strtoupper($n['author_name'][0])?></div>
          <span><?=h(explode(' ',$n['author_name'])[0])?></span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <span><?=fmt_relative($n['published_at']??$n['created_at'])?></span>
          <a href="news-view.php?id=<?=$n['id']?>" class="btn btn-outline btn-xs" style="border-radius:100px">Ler →</a>
        </div>
      </div>
      <?php if(current_user()): ?>
      <div style="display:flex;gap:6px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border2)">
        <a href="news-create.php?edit=<?=$n['id']?>" class="btn btn-outline btn-xs" style="flex:1;justify-content:center">✎ Editar</a>
        <a href="news-delete.php?id=<?=$n['id']?>" class="btn btn-danger btn-xs"
           onclick="return confirm('Excluir esta notícia?')" style="flex:1;justify-content:center">🗑 Excluir</a>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- PAGINAÇÃO -->
<?php if($lastPage > 1): ?>
<div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;margin-bottom:40px;flex-wrap:wrap">
  <?php if($page>1):?>
  <a href="?<?=http_build_query(array_merge($_GET,['page'=>$page-1]))?>" class="btn btn-outline btn-sm">← Anterior</a>
  <?php endif;?>
  <?php for($i=1;$i<=$lastPage;$i++):?>
  <a href="?<?=http_build_query(array_merge($_GET,['page'=>$i]))?>"
     style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;
            border-radius:4px;border:1px solid <?=$i===$page?'var(--text)':'var(--border)'?>;
            background:<?=$i===$page?'var(--text)':'var(--surface)'?>;
            color:<?=$i===$page?'var(--bg)':'var(--text2)'?>;font-size:13px;font-weight:500;
            text-decoration:none"><?=$i?></a>
  <?php endfor;?>
  <?php if($page<$lastPage):?>
  <a href="?<?=http_build_query(array_merge($_GET,['page'=>$page+1]))?>" class="btn btn-outline btn-sm">Próxima →</a>
  <?php endif;?>
  <span style="font-size:12px;color:var(--text3);margin-left:8px">Página <?=$page?> de <?=$lastPage?> · <?=$total?> notícias</span>
</div>
<?php endif; ?>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
