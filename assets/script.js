function confirmDelete(message) {
    return confirm(message || 'Tem certeza que deseja excluir este item?');
}

function toggleSection(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
