// DORI Interactive Features
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to sidebar items
    const sidebarItems = document.querySelectorAll('.dori-sidebar-item');
    sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            sidebarItems.forEach(i => i.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
            console.log('Selected:', this.textContent);
        });
    });

    // Add click handlers to cards
    const cards = document.querySelectorAll('.dori-card');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            const cardText = this.querySelector('span:last-child')?.textContent || 'Document';
            console.log('Opened:', cardText);
            // You can add navigation or modal functionality here
        });

        // Add hover effect
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Profile icon click handler
    const profileIcon = document.querySelector('.dori-header-icons img');
    if (profileIcon) {
        profileIcon.addEventListener('click', function() {
            console.log('Profile clicked');
            // Add profile menu functionality here
        });
    }
});
