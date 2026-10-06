<footer class="border-t border-gray-200 dark:border-gray-800 pb-28 lg:pb-0">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400 text-center sm:text-left">
        <span>
            &copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME, ENT_QUOTES) ?>.
            <?= __('footer_rights') ?>
            by <span class="font-semibold text-brand-600 dark:text-sage-300">NajmiNasrudin</span>
        </span>

        <div class="flex items-center gap-5">
            <a href="tel:+60122541050" class="flex items-center gap-1.5 hover:text-brand-600 dark:hover:text-sage-300 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                +60122541050
            </a>
            <a href="mailto:bizbuddyhq@gmail.com" class="flex items-center gap-1.5 hover:text-brand-600 dark:hover:text-sage-300 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                bizbuddyhq@gmail.com
            </a>
        </div>
    </div>
</footer>
