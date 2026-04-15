<?php
require_once 'includes/config.php';
require_login();
$editId = (int)($_GET['edit'] ?? 0);
$n = null;
if ($editId) {
    $n = DB::row('SELECT * FROM news WHERE id=?', [$editId]);
    if (!$n) { flash('error','Notícia não encontrada.'); header('Location: dashboard.php'); exit; }
}
$isEdit = $n !== null;
$pageTitle = $isEdit ? 'Editar Notícia' : 'Nova Notícia';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']   ?? '');
    $cat     = trim($_POST['category'] ?? '');
    $status  = $_POST['status']  ?? 'draft';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $image   = trim($_POST['image']   ?? '');
    $slug    = slugify($title);

    if (!$title)   $errors[] = 'O título é obrigatório.';
    if (!$excerpt) $errors[] = 'A chamada é obrigatória.';
    if (!$content) $errors[] = 'O conteúdo é obrigatório.';

    if (!$errors) {
        // garante slug único
        $count = (int)DB::scalar('SELECT COUNT(*) FROM news WHERE slug=? AND id!=?', [$slug, $editId]);
        if ($count) $slug .= '-' . time();

        if ($isEdit) {
            DB::exec('UPDATE news SET title=?,slug=?,category=?,status=?,excerpt=?,content=?,image_url=?,
                      published_at=IF(status!=? AND ?=\'published\',NOW(),published_at),updated_at=NOW()
                      WHERE id=?',
                [$title,$slug,$cat,$status,$excerpt,$content,$image,$n['status'],$status,$editId]);
            flash('success','Notícia atualizada com sucesso!');
        } else {
            $pubAt = $status==='published' ? date('Y-m-d H:i:s') : null;
            DB::insert('INSERT INTO news (author_id,title,slug,category,status,excerpt,content,image_url,published_at,created_at)
                        VALUES (?,?,?,?,?,?,?,?,?,NOW())',
                [current_user()['id'],$title,$slug,$cat,$status,$excerpt,$content,$image,$pubAt]);
            flash('success','Notícia publicada com sucesso!');
        }
        header('Location: dashboard.php'); exit;
    }
    $n = array_merge($n??[],$_POST);
}

include 'includes/header.php';
?>
<div class="page-hd">
  <div class="page-hd-left">
    <h1><?=$isEdit?'Editar Notícia':'Nova Notícia'?></h1>
    <p><?=$isEdit?'Atualize as informações abaixo':'Preencha os campos para publicar'?></p>
  </div>
  <div class="page-hd-actions"><a href="dashboard.php" class="btn btn-outline">← Cancelar</a></div>
</div>
<?php if($errors):?><div class="flash flash-error">✕ <?=implode(' &nbsp;|&nbsp; ',array_map('h',$errors))?></div><?php endif;?>
<form method="POST">
<div style="display:grid;grid-template-columns:1fr 300px;gap:22px;align-items:start">
  <div style="display:flex;flex-direction:column;gap:18px">
    <div class="card"><div class="card-hd"><h3>Conteúdo</h3></div><div class="card-body">
      <div class="form-group"><label class="form-label">Título *</label><input class="form-ctrl" type="text" name="title" value="<?=h($n['title']??'')?>" required placeholder="Título da notícia"></div>
      <div class="form-group" style="margin-bottom:0"><label class="form-label">Chamada / Resumo *</label><textarea class="form-ctrl" name="excerpt" rows="3" placeholder="Resumo atrativo para os cards…"><?=h($n['excerpt']??'')?></textarea></div>
    </div></div>
    <div class="card"><div class="card-hd"><h3>Corpo da Notícia</h3></div><div class="card-body">
      <div class="form-group" style="margin-bottom:0"><label class="form-label">Conteúdo completo *</label><textarea class="form-ctrl" name="content" rows="14" placeholder="Escreva o corpo completo aqui…"><?=h($n['content']??'')?></textarea></div>
    </div></div>
    <div class="card"><div class="card-hd"><h3>Imagem de Capa</h3></div><div class="card-body">
      <div class="form-group" style="margin-bottom:10px"><label class="form-label">URL da imagem</label>
        <input class="form-ctrl" type="text" name="image" id="imgInput" value="<?=h($n['image_url']??$n['image']??'')?>" placeholder="https://images.unsplash.com/…" oninput="previewImg()">
        <div class="form-hint">Cole URL de imagem de alta qualidade (Unsplash recomendado).</div>
      </div>
      <div id="imgPrev" style="display:<?=!empty($n['image_url']||$n['image']??'')?'block':'none'?>;height:180px;border-radius:var(--r);overflow:hidden;border:1px solid var(--border)">
        <img id="imgPrevEl" src="<?=h($n['image_url']??$n['image']??'')?>" style="width:100%;height:100%;object-fit:cover">
      </div>
      <div style="margin-top:12px"><div style="font-size:11px;color:var(--text3);margin-bottom:8px;letter-spacing:1px;text-transform:uppercase">Sugestões</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <?php $imgs=['Passarela'=>'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80','Beleza'=>'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80','Moda'=>'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80','Acessório'=>'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&q=80','Evento'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80'];
          foreach($imgs as $lbl=>$url):?>
          <button type="button" class="btn btn-outline btn-xs" style="border-radius:100px" onclick="document.getElementById('imgInput').value='<?=$url?>';previewImg()"><?=$lbl?></button>
          <?php endforeach;?>
        </div>
      </div>
    </div></div>
  </div>
  <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:80px">
    <div class="card"><div class="card-hd"><h3>Publicação</h3></div><div class="card-body">
      <div class="form-group"><label class="form-label">Status</label>
        <select class="form-ctrl" name="status">
          <option value="published" <?=($n['status']??'')==='published'?'selected':''?>>✓ Publicado</option>
          <option value="draft"     <?=($n['status']??'draft')==='draft'?'selected':''?>>◌ Rascunho</option>
        </select></div>
      <div class="form-group" style="margin-bottom:0"><label class="form-label">Categoria</label>
        <select class="form-ctrl" name="category">
          <?php foreach(['Moda','Beleza','Lifestyle','Tendências','Entrevistas','Eventos'] as $c):?>
          <option <?=($n['category']??'')===$c?'selected':''?>><?=h($c)?></option>
          <?php endforeach;?>
        </select></div>
    </div></div>
    <div class="card" style="background:var(--surface2)"><div class="card-body" style="padding:14px;display:flex;flex-direction:column;gap:8px">
      <button type="submit" class="btn btn-primary" style="justify-content:center;padding:12px">💾 Salvar</button>
      <a href="dashboard.php" class="btn btn-outline" style="justify-content:center">Cancelar</a>
    </div></div>
    <?php if($isEdit):?>
    <div class="card" style="border-color:var(--rose-bg)"><div class="card-body" style="padding:14px">
      <div style="font-size:11px;color:var(--text3);margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px">Zona de perigo</div>
      <a href="news-delete.php?id=<?=$editId?>" class="btn btn-danger" style="width:100%;justify-content:center" onclick="return confirm('Excluir esta notícia permanentemente?')">🗑 Excluir</a>
    </div></div>
    <?php endif;?>
  </div>
</div>
</form>
<script>
function previewImg(){const v=document.getElementById('imgInput').value.trim();const b=document.getElementById('imgPrev');const i=document.getElementById('imgPrevEl');if(v){b.style.display='block';i.src=v;}else b.style.display='none';}
</script>
<?php include 'includes/footer.php'; ?>
