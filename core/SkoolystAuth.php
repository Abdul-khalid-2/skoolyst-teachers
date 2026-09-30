<?php

/**
 * SkoolystAuth
 * "Login with Skoolyst" — OAuth2 Authorization Code client for the central
 * Skoolyst identity provider (skoolyst.com). The browser is only ever
 * redirected; the code-for-user exchange happens server-to-server so
 * SKOOLYST_AUTH_CLIENT_SECRET never reaches the client.
 */
class SkoolystAuth
{
    private const STATE_KEY = 'skoolyst_oauth_state';

    public static function isConfigured(): bool
    {
        return SKOOLYST_AUTH_CLIENT_ID !== '' && SKOOLYST_AUTH_CLIENT_SECRET !== '';
    }

    /**
     * Step 2: authorize URL. Stores a random `state` in the session (CSRF
     * protection) that the callback must echo back.
     */
    public static function authorizeUrl(): string
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION[self::STATE_KEY] = $state;

        return rtrim(SKOOLYST_AUTH_BASE, '/') . '/oauth/authorize?' . http_build_query([
            'client_id'    => SKOOLYST_AUTH_CLIENT_ID,
            'redirect_uri' => SKOOLYST_AUTH_REDIRECT_URI,
            'state'        => $state,
        ]);
    }

    /**
     * Single-use check of the `state` returned to the callback.
     */
    public static function verifyState(?string $state): bool
    {
        $expected = $_SESSION[self::STATE_KEY] ?? '';
        unset($_SESSION[self::STATE_KEY]);
        return $expected !== '' && is_string($state) && hash_equals($expected, $state);
    }

    /**
     * Steps 3-4: exchange the one-time code for the user's identity.
     * Returns ['id', 'name', 'email', 'email_verified'] or null on failure.
     */
    public static function exchangeCode(string $code): ?array
    {
        $ch = curl_init(rtrim(SKOOLYST_AUTH_BASE, '/') . '/api/oauth/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_POSTFIELDS     => json_encode([
                'client_id'     => SKOOLYST_AUTH_CLIENT_ID,
                'client_secret' => SKOOLYST_AUTH_CLIENT_SECRET,
                'code'          => $code,
                'redirect_uri'  => SKOOLYST_AUTH_REDIRECT_URI,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            // skoolyst.com publishes an AAAA record, but its IPv6 route times
            // out; browsers fall back to IPv4 on their own, curl doesn't.
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_CONNECTTIMEOUT => 10, // includes the TLS handshake
            CURLOPT_TIMEOUT        => 20,
        ]);

        $raw       = curl_exec($ch);
        $status    = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            error_log('[SkoolystAuth] Token request failed: ' . $curlError);
            return null;
        }

        $response = json_decode((string) $raw, true);
        $user     = $response['data']['user'] ?? null;

        if ($status !== 201 || empty($response['success']) || empty($user['id']) || empty($user['email'])) {
            $code    = $response['error']['code'] ?? 'unknown';
            $message = $response['error']['message'] ?? (string) $raw;
            error_log("[SkoolystAuth] Token exchange failed (HTTP {$status}, {$code}): {$message}");
            return null;
        }

        return [
            'id'             => (int) $user['id'],
            'name'           => trim((string) ($user['name'] ?? '')),
            'email'          => strtolower(trim((string) $user['email'])),
            'email_verified' => !empty($user['email_verified']),
        ];
    }
}
