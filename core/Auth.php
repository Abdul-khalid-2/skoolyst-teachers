<?php

/**
 * Auth
 * Session-backed authentication. Single `teachers` table holds both
 * roles ('teacher' | 'super-admin') as requested — no separate admin table.
 */
class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = Teacher::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        self::login($user);
        return true;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return Teacher::find($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['role'] ?? null) === 'super-admin';
    }
}
