<footer class="mt-auto bg-gray-900 dark:bg-gray-950 border-t border-gray-800 text-gray-400 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-brand-600 rounded flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <rect x="1.5" y="11" width="4" height="7.5" rx="1"/>
                        <rect x="7.5" y="6.5" width="4" height="12" rx="1"/>
                        <rect x="13.5" y="2.5" width="4" height="16" rx="1"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">
                    &copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME, ENT_QUOTES) ?>.
                    <?= __('footer_rights') ?>
                    <span class="text-gray-600">by <span class="font-medium text-brand-400">NajmiNasrudin</span></span>
                </span>
            </div>

            <div class="flex items-center gap-5 text-sm">
                <a href="tel:+60122541050"
                   class="flex items-center gap-1.5 text-gray-500 hover:text-brand-400 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    +60122541050
                </a>
                <a href="mailto:bizbuddyhq@gmail.com"
                   class="flex items-center gap-1.5 text-gray-500 hover:text-brand-400 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    bizbuddyhq@gmail.com
                </a>
            </div>
        </div>
    </div>
</footer>
