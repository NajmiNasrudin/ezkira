/**
 * SME Finance Monitor — Global JavaScript
 */

// ============================================================
// CSRF helper
// ============================================================
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

/**
 * POST JSON with CSRF header. Returns parsed JSON response.
 */
async function postJson(url, data = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-Token':  getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data),
    });
    return response.json();
}

// ============================================================
// Dark Mode Toggle
// ============================================================
async function toggleDarkMode() {
    const html    = document.documentElement;
    const isDark  = html.classList.toggle('dark');

    updateThemeIcon(isDark);
    if (typeof window.renderDashboardCharts === 'function') {
        window.renderDashboardCharts();
    }

    try {
        await postJson((window.BASE_URI || '') + '/theme/toggle');
    } catch (e) {
        // Revert on failure
        html.classList.toggle('dark', !isDark);
        updateThemeIcon(!isDark);
        console.error('Theme toggle failed:', e);
    }
}

function updateThemeIcon(isDark) {
    const sun  = document.getElementById('icon-sun');
    const moon = document.getElementById('icon-moon');
    if (!sun || !moon) return;

    if (isDark) {
        sun.classList.remove('hidden');
        moon.classList.add('hidden');
    } else {
        sun.classList.add('hidden');
        moon.classList.remove('hidden');
    }
}

// ============================================================
// Mobile Navigation
// ============================================================
function toggleMobileMenu() {
    var toggle = document.getElementById('nav-toggle');
    if (toggle) toggle.checked = !toggle.checked;
}

function closeMobileMenu() {
    var toggle = document.getElementById('nav-toggle');
    if (toggle) toggle.checked = false;
}

// ============================================================
// User Dropdown
// ============================================================
function toggleUserMenu() {
    var dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close menus when clicking outside
document.addEventListener('click', function (e) {
    // Close user dropdown
    var wrapper  = document.getElementById('user-menu-wrapper');
    var dropdown = document.getElementById('user-dropdown');
    if (wrapper && dropdown && !wrapper.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
    // Close mobile menu if click is outside hamburger + menu
    var hamburger  = document.getElementById('hamburger');
    var mobileMenu = document.getElementById('mobile-menu');
    if (hamburger && mobileMenu
            && !hamburger.contains(e.target)
            && !mobileMenu.contains(e.target)) {
        closeMobileMenu();
    }
});

// ============================================================
// Flash message auto-dismiss
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const flashes = document.querySelectorAll('.flash-message');
    flashes.forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(-6px)';
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });
});

// ============================================================
// Profile tab switching (used in profile/index.php)
// ============================================================
function switchTab(tabId) {
    // Hide all panels
    document.querySelectorAll('.tab-panel').forEach(function (panel) {
        panel.classList.add('hidden');
    });

    // Deactivate all tab buttons
    document.querySelectorAll('[id^="tab-"]').forEach(function (btn) {
        btn.classList.remove(
            'border-brand-600', 'text-brand-600',
            'dark:border-brand-400', 'dark:text-brand-400'
        );
        btn.classList.add(
            'border-transparent',
            'text-gray-500', 'dark:text-gray-400',
            'hover:text-gray-700', 'dark:hover:text-gray-300',
            'hover:border-gray-300', 'dark:hover:border-gray-600'
        );
    });

    // Show active panel
    const panel = document.getElementById('panel-' + tabId);
    if (panel) {
        panel.classList.remove('hidden');
    }

    // Activate tab button
    const btn = document.getElementById('tab-' + tabId);
    if (btn) {
        btn.classList.remove(
            'border-transparent',
            'text-gray-500', 'dark:text-gray-400',
            'hover:text-gray-700', 'dark:hover:text-gray-300',
            'hover:border-gray-300', 'dark:hover:border-gray-600'
        );
        btn.classList.add(
            'border-brand-600', 'text-brand-600',
            'dark:border-brand-400', 'dark:text-brand-400'
        );
    }
}
