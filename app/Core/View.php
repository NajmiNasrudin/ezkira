<?php

namespace App\Core;

class View
{
    /**
     * Render a view template inside a layout.
     *
     * @param string $template  e.g. 'auth/login' or 'dashboard/index'
     * @param array  $data      Variables extracted into view scope
     * @param string $layout    Layout name: 'main' or 'auth'
     * @param string $title     Page title
     */
    public static function render(
        string $template,
        array $data = [],
        string $layout = 'main',
        string $title = ''
    ): string {
        // Render the view fragment
        $viewFile = BASE_PATH . '/views/' . $template . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$template}");
        }

        // Make data available inside view + layout
        extract($data, EXTR_SKIP);
        $pageTitle = $title ?: APP_NAME;

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Render layout, injecting $content
        $layoutFile = BASE_PATH . '/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layout}");
        }

        ob_start();
        include $layoutFile;
        return ob_get_clean();
    }
}
