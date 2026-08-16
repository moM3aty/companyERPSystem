/* Path: resources/assets/js/app.js */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Sidebar Toggle Logic
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            // Store preference in localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed);
        });

        // Restore state on load
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }
    }

    // 2. Dropdown Menus Logic
    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const targetId = toggle.getAttribute('data-dropdown-toggle');
            const targetMenu = document.getElementById(targetId);
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== targetId) {
                    menu.classList.add('hidden');
                }
            });

            if (targetMenu) {
                targetMenu.classList.toggle('hidden');
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    });

    // Prevent Dropdown from closing when clicking inside it
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    console.log('Nour Trust ERP - Core JS Initialized.');
});


/**
 * Format numbers as currency dynamically based on locale.
 */
window.formatCurrency = function(amount, currency = 'USD', locale = 'en-US') {
    return new Intl.NumberFormat(locale, { style: 'currency', currency: currency }).format(amount);
};

/**
 * Global Custom Alert / Toast Notification System
 * Replaces default alert() with beautiful UI toasts.
 */
window.showToast = function(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;

    const toast = document.createElement('div');
    
    // Set colors based on type
    let bgColor, icon;
    if (type === 'success') { bgColor = 'bg-green-500'; icon = 'fa-check-circle'; }
    else if (type === 'error') { bgColor = 'bg-red-500'; icon = 'fa-exclamation-circle'; }
    else if (type === 'warning') { bgColor = 'bg-yellow-500'; icon = 'fa-exclamation-triangle'; }
    else { bgColor = 'bg-blue-500'; icon = 'fa-info-circle'; }

    toast.className = `flex items-center p-4 mb-4 w-full max-w-xs text-white ${bgColor} rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-full`;
    
    toast.innerHTML = `
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-white/20">
            <i class="fas ${icon}"></i>
        </div>
        <div class="ml-3 text-sm font-normal">${message}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white/10 text-white hover:bg-white/20 rounded-lg focus:ring-2 focus:ring-white p-1.5 inline-flex h-8 w-8 items-center justify-center" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    toastContainer.appendChild(toast);

    // Animate in
    setTimeout(() => toast.classList.remove('translate-x-full'), 10);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};