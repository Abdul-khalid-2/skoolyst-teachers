<?php

abstract class Controller
{
    protected function input(string $key, $default = null)
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth(): array
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        return Auth::user();
    }

    protected function requireAdmin(): array
    {
        $user = $this->requireAuth();
        if (($user['role'] ?? '') !== 'super-admin') {
            http_response_code(403);
            View::render('errors/404');
            exit;
        }
        return $user;
    }

    protected function csrfField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . Helpers::csrfToken() . '">';
    }

    protected function verifyCsrf(): void
    {
        $token = $this->input('_csrf');
        if (!Helpers::checkCsrf($token)) {
            http_response_code(419);
            die('Invalid or expired form submission. Please go back and try again.');
        }
    }
}
