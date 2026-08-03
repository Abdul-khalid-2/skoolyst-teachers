<?php

class View
{
    /**
     * Render a view file with optional layout wrapper.
     * @param string $view  dot/slash path relative to /views, e.g. 'home/index'
     * @param array  $data  variables extracted into the view's scope
     */
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view);
            return;
        }
        require $viewFile;
    }

    public static function partial(string $view, array $data = []): void
    {
        self::render($view, $data);
    }
}
