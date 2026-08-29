<?php

/**
 * AdEngine
 * Minimal, dependency-free client for the Skoolyst Ads (AdEngine) API.
 * Docs: https://ads.skoolyst.com/api-docs.php
 *
 * Every method fails soft (returns null / false) instead of throwing, so a
 * slow or down ads service never breaks a page render.
 */
class AdEngine
{
    /** Simple file-based cache so we don't hit the API on every request. */
    private static function cacheGet(string $key)
    {
        $file = sys_get_temp_dir() . '/adengine_' . md5($key) . '.json';
        if (!is_file($file)) {
            return null;
        }
        $raw = @file_get_contents($file);
        $data = $raw ? json_decode($raw, true) : null;
        if (!is_array($data) || !isset($data['expires'], $data['value'])) {
            return null;
        }
        if (time() > $data['expires']) {
            return null;
        }
        return $data['value'];
    }

    private static function cachePut(string $key, $value, int $ttlSeconds): void
    {
        $file = sys_get_temp_dir() . '/adengine_' . md5($key) . '.json';
        @file_put_contents($file, json_encode([
            'expires' => time() + $ttlSeconds,
            'value'   => $value,
        ]));
    }

    /**
     * Low-level request helper. Returns the decoded 'data' array on success,
     * or null on any failure (network error, non-2xx, malformed body).
     */
    private static function request(string $method, string $path, array $body = null)
    {
        if (empty(ADS_API_BASE) || empty(ADS_API_KEY)) {
            return null; // not configured — treat as "no ads available"
        }

        $url = rtrim(ADS_API_BASE, '/') . '/' . ltrim($path, '/');

        $ch = curl_init($url);
        $headers = ['Authorization: Bearer ' . ADS_API_KEY];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        if ($method === 'POST') {
            $payload = $body ? json_encode($body) : '{}';
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $error) {
            return null;
        }
        if ($status < 200 || $status >= 300) {
            return null;
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded) || empty($decoded['success'])) {
            return null;
        }

        return $decoded['data'] ?? [];
    }

    /**
     * Get one eligible ad for a placement, or null if none is active.
     * Cached for 25s per placement to stay under the API's own 30s cache
     * window and avoid an outbound call on every single pageview.
     */
    public static function getAd(string $placement): ?array
    {
        $cacheKey = 'ad_' . $placement;
        $cached = self::cacheGet($cacheKey);
        if ($cached !== null) {
            return $cached['ad'] ?? null;
        }

        $data = self::request('GET', '/ads/serve?placement=' . urlencode($placement));
        if ($data === null) {
            // Don't cache failures — retry on next request instead of
            // hiding ads for a full 25s after a transient blip.
            return null;
        }

        self::cachePut($cacheKey, $data, 25);
        return $data['ad'] ?? null;
    }

    /**
     * Resolve an ad's relative image_path against the AdEngine app's root
     * host (images are served from the app root, not under /api/v1).
     */
    public static function imageUrl(string $imagePath): string
    {
        $root = preg_replace('#/api/v\d+/?$#', '', ADS_API_BASE);
        return rtrim($root, '/') . '/' . ltrim($imagePath, '/');
    }

    public static function trackImpression(int $adId): bool
    {
        return self::request('POST', '/ads/' . $adId . '/impression', ['ad_id' => $adId]) !== null;
    }

    public static function trackClick(int $adId): bool
    {
        return self::request('POST', '/ads/' . $adId . '/click', ['ad_id' => $adId]) !== null;
    }
}
