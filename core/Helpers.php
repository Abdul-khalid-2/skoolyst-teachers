<?php

class Helpers
{
    public static function uuid4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        return $text ?: 'teacher';
    }

    public static function uniqueSlug(string $base): string
    {
        $slug = self::slugify($base);
        $original = $slug;
        $i = 1;
        while (Teacher::findBy('slug', $slug)) {
            $slug = $original . '-' . $i;
            $i++;
        }
        return $slug;
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    /** mbstring-safe substr fallback for hosts without the extension enabled */
    public static function strimwidth(?string $text, int $length, string $suffix = ''): string
    {
        $text = (string) $text;
        if (function_exists('mb_strimwidth')) {
            return mb_strimwidth($text, 0, $length, $suffix);
        }
        return strlen($text) > $length ? substr($text, 0, $length) . $suffix : $text;
    }

    public static function firstName($name)
    {
        return explode(' ', trim($name))[0];
    }


    public static function initial(?string $text): string
    {
        $text = trim((string) $text);
        if ($text === '') return '';
        return function_exists('mb_substr') ? mb_strtoupper(mb_substr($text, 0, 1)) : strtoupper(substr($text, 0, 1));
    }

    public static function jsonDecode(?string $value): array
    {
        if (empty($value)) {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function jsonEncode(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function checkCsrf(?string $token): bool
    {
        return !empty($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }

    public static function asset(string $path): string
    {
        return ASSETS_URL . '/' . ltrim($path, '/');
    }

    public static function url(string $path = ''): string
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }

    public static function flash(string $key, ?string $message = null)
    {
        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
            return null;
        }
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function old(string $key, $default = '')
    {
        $value = $_SESSION['_old'][$key] ?? $default;
        return $value;
    }

    public static function setOld(array $data): void
    {
        $_SESSION['_old'] = $data;
    }

    public static function clearOld(): void
    {
        unset($_SESSION['_old']);
    }

    public static function timeAgo(?string $datetime): string
    {
        if (!$datetime) return '';
        $diff = time() - strtotime($datetime);
        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
        return floor($diff / 86400) . ' days ago';
    }
}
