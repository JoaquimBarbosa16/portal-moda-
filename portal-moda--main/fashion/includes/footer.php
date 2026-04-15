<?php // footer.php ?>
</div><!-- .content -->
</div><!-- .main-wrap -->
</div><!-- .app -->

<!-- MODAL CONFIRM DELETE -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal" style="max-width:420px">
    <div class="modal-hd"><h3>Confirmar exclusão</h3><button class="modal-close" onclick="document.getElementById('confirmModal').classList.remove('open')">✕</button></div>
    <div class="modal-body"><p id="confirmMsg" style="color:var(--text2);font-size:14px;line-height:1.6">Tem certeza que deseja excluir?</p></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('confirmModal').classList.remove('open')">Cancelar</button>
      <a class="btn btn-danger" id="confirmBtn" href="#">Excluir</a>
    </div>
  </div>
</div>
<script>
function confirmDelete(msg, href) {
  document.getElementById('confirmMsg').textContent = msg;
  document.getElementById('confirmBtn').href = href;
  document.getElementById('confirmModal').classList.add('open');
}
document.getElementById('confirmModal').addEventListener('click', function(e){ if(e.target===this) this.classList.remove('open'); });
// Flash auto-hide
setTimeout(()=>{ const f=document.querySelector('.flash'); if(f) f.style.opacity='0'; }, 4000);
</script>
</body>
</html>
