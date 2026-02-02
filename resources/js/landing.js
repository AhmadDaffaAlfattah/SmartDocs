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
    item.addEventListener('click', function() {
        const menu = this.dataset.menu;
        console.log('Menu item clicked:', menu || this.textContent);
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Submenu is hidden by default, can add initialization here if needed
});
