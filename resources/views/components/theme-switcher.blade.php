{{-- Global Theme Switcher Component --}}
<script>
(function() {
    'use strict';
    
    // Theme management
    const ThemeManager = {
        init() {
            this.loadThemeSettings();
            this.setupSystemThemeListener();
        },
        
        loadThemeSettings() {
            // Get theme settings from server or localStorage
            const savedTheme = localStorage.getItem('theme_mode');
            const systemTheme = this.getSystemTheme();
            
            // Apply theme based on settings
            fetch('{{ route("admin.theme.generateCSS") }}')
                .then(response => response.text())
                .then(css => {
                    this.injectCSS(css);
                })
                .catch(error => {
                    console.warn('Could not load theme CSS:', error);
                });
            
            // Apply theme mode
            const themeMode = savedTheme || '{{ config("app.theme_mode", "light") }}';
            this.applyThemeMode(themeMode);
        },
        
        applyThemeMode(mode) {
            const html = document.documentElement;
            const body = document.body;
            
            // Remove existing theme classes
            html.removeAttribute('data-bs-theme');
            body.classList.remove('theme-light', 'theme-dark');
            
            switch(mode) {
                case 'dark':
                    html.setAttribute('data-bs-theme', 'dark');
                    body.classList.add('theme-dark');
                    break;
                case 'auto':
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (prefersDark) {
                        html.setAttribute('data-bs-theme', 'dark');
                        body.classList.add('theme-dark');
                    } else {
                        html.setAttribute('data-bs-theme', 'light');
                        body.classList.add('theme-light');
                    }
                    break;
                default: // light
                    html.setAttribute('data-bs-theme', 'light');
                    body.classList.add('theme-light');
                    break;
            }
            
            // Store preference
            localStorage.setItem('theme_mode', mode);
        },
        
        getSystemTheme() {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        },
        
        setupSystemThemeListener() {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addEventListener('change', (e) => {
                const currentMode = localStorage.getItem('theme_mode');
                if (currentMode === 'auto') {
                    this.applyThemeMode('auto');
                }
            });
        },
        
        injectCSS(css) {
            // Remove existing dynamic theme CSS
            const existingStyle = document.getElementById('dynamic-theme-css');
            if (existingStyle) {
                existingStyle.remove();
            }
            
            // Inject new CSS
            const style = document.createElement('style');
            style.id = 'dynamic-theme-css';
            style.textContent = css;
            document.head.appendChild(style);
        },
        
        // Public method to switch theme
        switchTheme(mode) {
            this.applyThemeMode(mode);
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ThemeManager.init());
    } else {
        ThemeManager.init();
    }
    
    // Make ThemeManager globally available
    window.ThemeManager = ThemeManager;
})();
</script>

{{-- Theme-specific CSS variables --}}
<style>
:root {
    --theme-transition: all 0.3s ease;
}

.theme-dark {
    --bs-body-bg: #212529;
    --bs-body-color: #ffffff;
    --bs-border-color: #495057;
}

.theme-light {
    --bs-body-bg: #ffffff;
    --bs-body-color: #212529;
    --bs-border-color: #dee2e6;
}

/* Smooth transitions for theme changes */
body, .card, .navbar, .sidebar {
    transition: var(--theme-transition);
}
</style>