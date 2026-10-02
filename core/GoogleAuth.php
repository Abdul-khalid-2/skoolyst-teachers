<?php

/**
 * GoogleAuth
 * "Continue with Google" — OAuth2 Authorization Code client talking to
 * Google directly (this app's own Google Cloud OAuth client). The browser is
 * only redirected; the code-for-profile exchange happens server-to-server so
 * GOOGLE_CLIENT_SECRET never reaches the client.
 */
class GoogleAuth
{
    private const STATE_KEY = 'google_oauth_state';

    private const AUTHORIZE_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL     = 'https://oauth2.googleapis.com/token';
    private const USERINFO_URL  = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public static function isConfigured(): bool
    {
        return GOOGLE_CLIENT_ID !== '' && GOOGLE_CLIENT_SECRET !== '';
    }

    /**
     * Step 2: Google consent-screen URL. Stores a random `state` in the
     * session (CSRF protection) that the callback must echo back.
     */
    public static function authorizeUrl(): string
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION[self::STATE_KEY] = $state;

        return self::AUTHORIZE_URL . '?' . http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'prompt'        => 'select_account',
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
     * Steps 3-4: exchange the one-time code for tokens, then read the
     * profile. Returns ['id', 'name', 'email', 'email_verified'] or null.
     */
    public static function exchangeCode(string $code): ?array
    {
        [$status, $token] = self::request(self::TOKEN_URL, [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'code'          => $code,
                'client_id'     => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
                'redirect_uri'  => GOOGLE_REDIRECT_URI,
                'grant_type'    => 'authorization_code',
            ]),
        ]);
        if ($status !== 200 || empty($token['access_token'])) {
            error_log('[GoogleAuth] Token exchange failed (HTTP ' . $status . '): ' . json_encode($token));
            return null;
        }

        [$status, $profile] = self::request(self::USERINFO_URL, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token['access_token']],
        ]);
        if ($status !== 200 || empty($profile['sub']) || empty($profile['email'])) {
            error_log('[GoogleAuth] Userinfo request failed (HTTP ' . $status . '): ' . json_encode($profile));
            return null;
        }

        $email = strtolower(trim((string) $profile['email']));
        return [
            'id'             => (string) $profile['sub'],
            'name'           => trim((string) ($profile['name'] ?? '')),
            'email'          => $email,
            'email_verified' => filter_var($profile['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /**
     * @return array{0:int, 1:?array} HTTP status and decoded JSON body.
     */
    private static function request(string $url, array $options): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, $options + [
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            // Same as SkoolystAuth: avoid hanging on a broken IPv6 route.
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_CONNECTTIMEOUT => 10, // includes the TLS handshake
            CURLOPT_TIMEOUT        => 20,
        ]);

        $raw    = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($raw === false) {
            error_log('[GoogleAuth] Request to ' . $url . ' failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return [$status, $raw === false ? null : json_decode((string) $raw, true)];
    }
}
