<?php
require_once 'includes/config.php';
require_admin();
$pageTitle = 'Usuários';

// Delete
if(($_GET['action']??'')==='delete' && ($did=(int)($_GET['id']??0))){
    if($did!==current_user()['id']){ DB::exec('DELETE FROM users WHERE id=?',[$did]); flash('info','Usuário excluído.');}
    else flash('error','Não é possível excluir a própria conta.');
    header('Location: admin-users.php'); exit;
}
// Toggle active
if(($_GET['action']??'')==='toggle' && ($tid=(int)($_GET['id']??0))){
    DB::exec('UPDATE users SET active=NOT active WHERE id=?',[$tid]);
    header('Location: admin-users.php'); exit;
}
// Save
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $eid  =(int)($_POST['edit_id']??0);
    $name =trim($_POST['name']??'');
    $email=strtolower(trim($_POST['email']??''));
    $pass =$_POST['pass']??'';
    $role =$_POST['role']==='admin'?'admin':'user';
    if(!$name||!$email) $errors[]='Nome e e-mail são obrigatórios.';
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='E-mail inválido.';
    elseif(!$eid&&!$pass) $errors[]='Informe uma senha.';
    elseif($pass&&strlen($pass)<6) $errors[]='Senha deve ter 6+ caracteres.';
    elseif((int)DB::scalar('SELECT COUNT(*) FROM users WHERE email=? AND id!=?',[$email,$eid])) $errors[]='E-mail já cadastrado.';
    if(!$errors){
        if($eid){
            $sql='UPDATE users SET name=?,email=?,role=? WHERE id=?';$p=[$name,$email,$role,$eid];
            if($pass){$sql='UPDATE users SET name=?,email=?,role=?,password=? WHERE id=?';$p=[$name,$email,$role,password_hash($pass,PASSWORD_DEFAULT),$eid];}
            DB::exec($sql,$p);
            if($eid===current_user()['id']){$u=DB::row('SELECT * FROM users WHERE id=?',[$eid]);unset($u['password']);$_SESSION['user']=$u;}
            flash('success','Usuário atualizado!');
        } else {
            DB::insert('INSERT INTO users (name,email,password,role,created_at) VALUES (?,?,?,?,NOW())',[$name,$email,password_hash($pass,PASSWORD_DEFAULT),$role]);
            flash('success','Usuário criado com sucesso!');
        }
        header('Location: admin-users.php'); exit;
    }
}
// List
$search=trim($_GET['q']??'');$roleF=$_GET['role']??'';
$where='1=1';$params=[];
if($search){$where.=' AND (name LIKE ? OR email LIKE ?)';$params[]="%$search%";$params[]="%$search%";}
if($roleF){$where.=' AND role=?';$params[]=$roleF;}
$users=DB::query("SELECT * FROM users WHERE $where ORDER BY id DESC",$params);
$editUser=null;
if(($_GET['action']??'')==='edit'&&($eid=(int)($_GET['id']??0))){$editUser=DB::row('SELECT * FROM users WHERE id=?',[$eid]);}
$showForm=(($_GET['action']??'')==='new')||(($_GET['action']??'')==='edit')||!empty($errors);
include 'includes/header.php';
?>
<div class="page-hd">
  <div class="page-hd-left"><h1>Gerenciar Usuários</h1><p><?=count($users)?> usuário(s) encontrado(s)</p></div>
  <div class="page-hd-actions"><a href="?action=new" class="btn btn-primary">+ Novo Usuário</a></div>
</div>
<?php if($errors):?><div class="flash flash-error">✕ <?=implode(' | ',array_map('h',$errors))?></div><?php endif;?>
<?php if($showForm):?>
<div class="card" style="margin-bottom:24px;border-color:var(--gold)">
  <div class="card-hd" style="background:var(--gold-bg2)">
    <h3><?=$editUser?'Editar Usuário':'Novo Usuário'?></h3>
    <a href="admin-users.php" class="btn btn-outline btn-sm">✕ Cancelar</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="edit_id" value="<?=$editUser['id']??''?>">
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Nome *</label><input class="form-ctrl" type="text" name="name" value="<?=h($_POST['name']??($editUser['name']??''))?>" required placeholder="Nome completo"></div>
        <div class="form-group"><label class="form-label">E-mail *</label><input class="form-ctrl" type="email" name="email" value="<?=h($_POST['email']??($editUser['email']??''))?>" required placeholder="email@exemplo.com"></div>
        <div class="form-group"><label class="form-label">Senha <?=$editUser?'(nova)':'*'?></label><input class="form-ctrl" type="password" name="pass" placeholder="<?=$editUser?'Deixe vazio para manter':'Mínimo 6 caracteres'?>"></div>
        <div class="form-group"><label class="form-label">Perfil</label>
          <select class="form-ctrl" name="role">
            <option value="user" <?=($_POST['role']??($editUser['role']??''))==='user'?'selected':''?>>Usuário</option>
            <option value="admin" <?=($_POST['role']??($editUser['role']??''))==='admin'?'selected':''?>>Admin</option>
          </select></div>
      </div>
      <div class="form-actions"><button type="submit" class="btn btn-primary">💾 Salvar</button><a href="admin-users.php" class="btn btn-outline">Cancelar</a></div>
    </form>
  </div>
</div>
<?php endif;?>
<div class="tbl-filters">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="search-wrap" style="flex:1;min-width:180px"><span class="search-icon">🔍</span><input class="search-input" type="text" name="q" value="<?=h($search)?>" placeholder="Buscar por nome ou e-mail…"></div>
    <select class="filter-sel" name="role" onchange="this.form.submit()"><option value="">Todos os perfis</option><option value="admin" <?=$roleF==='admin'?'selected':''?>>Admin</option><option value="user" <?=$roleF==='user'?'selected':''?>>Usuário</option></select>
    <button class="btn btn-outline" type="submit">Buscar</button>
    <?php if($search||$roleF):?><a href="admin-users.php" class="btn btn-outline">✕</a><?php endif;?>
  </form>
</div>
<div class="tbl-wrap"><table>
  <thead><tr><th>Usuário</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Cadastro</th><th>Ações</th></tr></thead>
  <tbody>
  <?php if(empty($users)):?>
  <tr><td colspan="6"><div style="text-align:center;padding:40px;color:var(--text3)"><div style="font-size:36px;opacity:.2;margin-bottom:12px">◷</div><p>Nenhum usuário encontrado.</p></div></td></tr>
  <?php else:?>
  <?php foreach($users as $u):?>
  <tr>
    <td><div style="display:flex;align-items:center;gap:10px">
      <div class="avatar"><?=strtoupper($u['name'][0])?></div>
      <div><strong><?=h($u['name'])?></strong><?=$u['id']===current_user()['id']?'<span style="font-size:10px;color:var(--gold);margin-left:6px">(você)</span>':''?></div>
    </div></td>
    <td style="color:var(--text3);font-size:13px"><?=h($u['email'])?></td>
    <td><span class="badge <?=$u['role']==='admin'?'badge-admin':'badge-user'?>"><?=$u['role']==='admin'?'Admin':'Usuário'?></span></td>
    <td><span class="badge <?=$u['active']?'badge-pub':'badge-draft'?>"><?=$u['active']?'Ativo':'Inativo'?></span></td>
    <td style="font-size:12px;color:var(--text3)"><?=fmt_date($u['created_at'])?></td>
    <td><div class="td-actions">
      <a href="?action=edit&id=<?=$u['id']?>" class="btn btn-outline btn-sm">✎ Editar</a>
      <a href="?action=toggle&id=<?=$u['id']?>" class="btn btn-outline btn-sm"><?=$u['active']?'Desativar':'Ativar'?></a>
      <?php if($u['id']!==current_user()['id']):?>
      <a href="?action=delete&id=<?=$u['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir o usuário <?=h($u['name'])?>?')">Excluir</a>
      <?php endif;?>
    </div></td>
  </tr>
  <?php endforeach;?>
  <?php endif;?>
  </tbody>
</table></div>
<?php include 'includes/footer.php'; ?>
