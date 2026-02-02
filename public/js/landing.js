// Collapsible Menu Toggle
document.getElementById('document-menu')?.addEventListener('click', function() {
    this.classList.toggle('open');
    const submenu = document.getElementById('submenu-document');
    submenu.classList.toggle('open');
});

// Account Menu Toggle
document.querySelector('.account-section')?.addEventListener('click', function() {
    const accountSubmenu = document.querySelector('.account-submenu');
    accountSubmenu.style.display = accountSubmenu.style.display === 'none' ? 'flex' : 'none';
});

// Submenu item click handler
document.querySelectorAll('.submenu-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.stopPropagation();
