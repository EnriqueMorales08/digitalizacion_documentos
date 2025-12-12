<!-- Botón de EDITAR cuando está en modo visualización -->
<!-- USO: Incluir este archivo en cada documento después del botón GUARDAR -->
<!-- IMPORTANTE: Reemplazar {DOCUMENT_ID} con el ID del documento -->
<?php if (isset($modoImpresion) && $modoImpresion): ?>
<div style="position: fixed; top: 80px; right: 20px; z-index: 1000;" class="no-print">
  <a href="/digitalizacion-documentos/documents/show?id={DOCUMENT_ID}&orden_id=<?php echo $_SESSION['orden_id'] ?? ''; ?>" 
     style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; border-radius: 25px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; transition: all 0.3s ease;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    ✏️ EDITAR
  </a>
</div>
<?php endif; ?>

<!-- Script para deshabilitar edición en modo visualización -->
<script>
<?php if (isset($modoImpresion) && $modoImpresion): ?>
document.addEventListener('DOMContentLoaded', function() {
  // Deshabilitar todos los contenteditable
  const editables = document.querySelectorAll('[contenteditable="true"]');
  editables.forEach(function(el) {
    el.setAttribute('contenteditable', 'false');
    el.style.cursor = 'default';
  });
  
  // Deshabilitar todos los inputs y selects
  const inputs = document.querySelectorAll('input:not([type="hidden"]), select, textarea');
  inputs.forEach(function(el) {
    el.setAttribute('readonly', 'readonly');
    el.setAttribute('disabled', 'disabled');
    el.style.cursor = 'default';
    el.style.pointerEvents = 'none';
  });
});
<?php endif; ?>
</script>
